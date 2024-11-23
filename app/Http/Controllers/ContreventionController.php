<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\User;
use Nette\Utils\Random;
use Illuminate\Http\Request;
use App\Models\contrevention;
use App\Models\contreventionUser;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StorecontreventionRequest;
use App\Http\Requests\UpdatecontreventionRequest;
use App\Http\Resources\contreventionUser as RescontreventionUser;
use App\Models\transaction;

class ContreventionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.home');
    }
    public function infraction()
    {
        $infractions = contrevention::get();
        return view('pages.register', compact('infractions'));
    }
    public function searchMatricule(Request $request)
    {


        return view('pages.home');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $login = User::where("phone", $request->phone)->first();

        if (!$login || !Hash::check($request->password, $login->password)) {
            return response()->json(
                [
                    'reponse' => false,
                    'msg' => 'Utilisateur non reconnu',
                ]
            );
        } else {
            return response()->json(
                [
                    'reponse' => true,
                    'msg' => 'Vous pouvez crée une contrevention!',
                    'data' => $login,
                ]
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

       $cli= User::where("matricule",$request->matricule)->first();

        if($cli){
            $ref = 'REF-' . ((string) random_int(10000000, 99999999));
            $inputs = [
                "contrevention_id" => $request->contrevention_id,
                "user_id" =>  $request->user_id,
                "matricule" => $request->matricule,
                "reference" => $ref,
                "payerPar" => $request->payerPar,
                "phone" => $request->phonepayant,
            ];


            $contrevention = contreventionUser::create($inputs);
            if (!$contrevention) {
                return response()->json(
                    [
                        'reponse' => false,
                        'msg' => 'Erreur de création!!',
                    ]
                );
            } else {
                $infra = contrevention::find($request->contrevention_id);
                $message = $cli->fisrtname . " " . $cli->name . " votre contrevention de la voiture imatriculée " . $cli->matricule . " avec reference " . $ref . " est de " . $infra->prix . $infra->monaie . " à payer dans 24h";
                $ret = $this->sendSms($cli->phone, $message);

                return response()->json(
                    [
                        'reponse' => true,
                        'msg' => 'La création est faite avec succès'
                    ]
                );
            }

        }else{
            return response()->json(
                [
                    'reponse' => false,
                    'msg' => 'Ce matricule est erroné',
                ]
            );
        }

    }
    public function paieInfraction(Request $request)
    {
        // dd($request);
        $inputs = [
            'transaction_type_id' => $request->toggleOption == 'mobile' ? 1 : 2,
            'amount' => $request->prix,
            'currency' => $request->monaie,
            'reference' => $request->reference,
            'other_phone' => $request->number,
            'app_url' => env("FLEXPAY_GATEWAY_CARD"),
        ];
        if ($request->toggleOption === "cash") {
            $login = User::where("phone", $request->phoneAgent)->first();

            if (!$login || !Hash::check($request->password, $login->password)) {

                return response()->json(
                    [
                        'reponse' => false,
                        'msg' => 'Agent non reconnu',
                    ]
                );
            } else {
                $infration = contreventionUser::where([["id", $request->contrevention], ['etat', '0']])->first();
                if ($infration) {
                    $infration->update([
                        'payerPar' => $login->name . " " . $login->fisrtname,
                        'phone' => $request->phoneAgent,
                        'etat' => "1",
                        'updated_at' => now(),
                    ]);
                    $cli= User::where("matricule",$request->matricule)->first();
                    $infra = contrevention::find($request->contrevention_id);
                $message = $cli->fisrtname . " " . $cli->name . " votre contrevention de reference " . $infration->reference . " à été soldée !";
                $ret = $this->sendSms($cli->phone, $message);

                    return response()->json(
                        [
                            'reponse' => true,
                            'msg' => 'Contrevention payer avec succès',
                        ]
                    );
                } else {
                    return response()->json(
                        [
                            'reponse' => false,
                            'msg' => 'Cette contrevention à été déjà payée!',
                        ]
                    );
                }
            }
        } elseif ($request->toggleOption === "mobile") {
            // Create response by sending request to FlexPay
            $data = array(
                'merchant' => env("FLEXPAY_MARCHAND"),
                'type' => $inputs["transaction_type_id"],
                'phone' => $inputs["other_phone"],
                'reference' => $inputs["reference"],
                'amount' => $inputs['amount'],
                'currency' => $inputs['currency'],
                // 'callbackUrl' => env('APP_URL') . 'storeTransaction',
                'callbackUrl' => 'https://dgrad.silasmas.com/storeTransaction'
            );
            $data = json_encode($data);
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, env("FLEXPAY_GATEWAY_MOBILE"));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('FLEXPAY_API_TOKEN')
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return response()->json(
                    [
                        'reponse' => false,
                        'msg' => 'Une erreur lors du traitement de votre requête'
                    ]
                );
            } else {
                curl_close($ch);

                $jsonRes = json_decode($response);
                $code = $jsonRes->code; // Push sending status

                if ($code != '0') {
                    return response()->json(
                        [
                            'reponse' => false,
                            'msg' => 'Impossible de traiter la demande, veuillez réessayer echec envoie du push'
                        ]
                    );
                } else {
                    $object = new stdClass();

                    $object->result_response = [
                        'message' => $jsonRes->message,
                        'order_number' => $jsonRes->orderNumber
                    ];
                    // $contre = contreventionUser::where('reference', $inputs["reference"])->first();
                    // // The donation is registered only if the processing succeed
                    // $contre->update(['etat' => '1']);

                    // // Register payment, even if FlexPay will
                    $payment = transaction::where('order_number', $jsonRes->orderNumber)->first();

                    if (is_null($payment)) {
                        transaction::create([
                            'reference' => $inputs["reference"],
                            'order_number' => $jsonRes->orderNumber,
                            'amount' => $inputs['amount'],
                            'phone' => $request->other_phone,
                            'currency' => $inputs['currency'],
                            'type_id' => $inputs["transaction_type_id"],
                        ]);
                    }
                    return response()->json(
                        [
                            'reponse' => true,
                            'msg' => 'Veuillez validé votre paiement sur votre téléphone!',
                            'orderNumber' => $jsonRes->orderNumber
                        ]
                    );
                }
            }
        } else {
            $body = json_encode(array(
                'authorization' => "Bearer " . env('FLEXPAY_API_TOKEN'),
                'merchant' => env('FLEXPAY_MARCHAND'),
                'reference' => $inputs['reference'],
                'amount' => $inputs['amount'],
                'currency' => $inputs['currency'],
                'description' => "Paiemen d'une contrevention",
                'callback_url' => env('APP_URL') . 'storeTransaction',
                'approve_url' =>  env('APP_URL') . 'paid/' . $inputs['amount'] . '/' . $inputs['currency'] . '/0',
                'cancel_url' =>  env('APP_URL') . 'paid/' . $inputs['amount'] . '/' . $inputs['currency'] . '/1',
                'decline_url' =>  env('APP_URL') . 'paid/' . $inputs['amount'] . '/' . $inputs['currency'] . '/2',
                'home_url' =>  env('APP_URL') . 'home',
            ));

            $curl = curl_init(env('FLEXPAY_GATEWAY_CARD'));
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            $curlResponse = curl_exec($curl);
            

            $jsonRes = json_decode($curlResponse, true);
            $code = $jsonRes['code'];
            $message = $jsonRes['message'];

            if (!empty($jsonRes['error'])) {
                return response()->json(
                    [
                        'reponse' => false,
                        'msg' => $jsonRes['message'],
                        'data' => $jsonRes['error']
                    ]
                );
            } else {
                if ($code != '0') {
                    return response()->json(
                        [
                            'reponse' => false,
                            'msg' => $jsonRes['message'],
                        'data' => $code
                        ]
                    );
                } else {
                    $url = $jsonRes['url'];
                    $orderNumber = $jsonRes['orderNumber'];
                    $object = new stdClass();

                    $object->result_response = [
                        'message' => $message,
                        'order_number' => $orderNumber,
                        'url' => $url
                    ];

                    // The donation is registered only if the processing succeed
                    // $contre = contreventionUser::where('reference', $inputs["reference"])->first();
                    // The donation is registered only if the processing succeed
                    // $contre->update(['etat' => '1']);

                    // // Register payment, even if FlexPay will
                    $payment = transaction::where('order_number', $jsonRes['orderNumber'])->first();

                    if (is_null($payment)) {
                        transaction::create([
                            'reference' => $inputs["reference"],
                            'order_number' => $jsonRes['orderNumber'],
                            'amount' => $inputs['amount'],
                            'phone' => $request->other_phone,
                            'currency' => $inputs['currency'],
                            'type_id' => $inputs["transaction_type_id"],
                        ]);
                    }
                    return response()->json(
                        [
                            'reponse' => true,
                            'msg' => 'Contrevention payée!',
                            'data' => $object,
                        ]
                    );
                }
            }
        }
    }

    public function paid($amount = null, $currency = null, $code)
    {
        // Find status by name API
        if ($code == '0') {
            return view('transaction_message', [
                'status_code' => $code,
                'message_content' => "Transaction effectuée"
            ]);
        }

        if ($code == '1') {
            $tr = new TransactionController();
            // Find payment by order number API
            $payment = $tr->findByOrderNumber(request()->get('orderNumber'));
            $ret = json_decode($payment);

            return view('pages.transaction_message', [
                'status_code' => $code,
                'message_content' => "Transaction annulée",
                'data' => $ret,
            ]);
        }

        if ($code == '2') {
            $tr = new TransactionController();
            // Find payment by order number API
            $payment = $tr->findByOrderNumber(request()->get('orderNumber'));
            $ret = json_decode($payment);


            return view('transaction_message', [
                'status_code' => $code,
                'message_content' => "Transaction echouée",
                'data' => $ret,
            ]);
        }
    }
    public function showInfra(Request $request)
    {

        $contrevention = contreventionUser::with(['user', 'contrevention'])
            ->where("reference", $request->ref)->first();
        if (!$contrevention) {
            return response()->json(
                [
                    'reponse' => false,
                    'msg' => 'Aucune contrevention trouvée avec cette référence!!',
                ]
            );
        } else {
            if ($contrevention->etat == '0') {
                return response()->json(
                    [
                        'reponse' => true,
                        'msg' => 'Resultat trouvé!',
                        'data' => [
                            'reference' => $contrevention,
                            'user' => $contrevention->user ?? 'Non spécifié',
                            'contrevention' => $contrevention->contrevention ?? 'Non spécifié'
                        ]

                    ],
                );
            } else {
                return response()->json(
                    [
                        'reponse' => false,
                        'msg' => 'Cette contrevention à été déjà payée!',

                    ],
                );
            }
        }
    }



    function sendSms($phoneNumber, $message)
    {
        // URL de l'API de Keccel (remplacez par l'URL réelle)
        $apiUrl = env('SMS_URL');

        // Clé API ou identifiants d'authentification (remplacez par vos informations)
        $apiKey = env('SMS_TOKEN');

        // Données à envoyer
        $postData = [
            "token" => $apiKey,    // taken
            "to" => $phoneNumber,    // Numéro de téléphone du destinataire
            "from" => env('SMS_FROM'), // Optionnel : Nom ou numéro de l'expéditeur
            "message" => $message,   // Contenu du message
        ];

        // Initialisation de cURL
        $ch = curl_init();

        // Configuration de la requête
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); // Conversion des données en JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey", // Clé API incluse dans les en-têtes
        ]);

        // Exécuter la requête
        $response = curl_exec($ch);

        // Vérifier les erreurs
        if (curl_errno($ch)) {
            echo "Erreur cURL : " . curl_error($ch);
        }

        // Décoder la réponse
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Afficher la réponse pour débogage
        return [
            "status_code" => $responseCode,
            "response" => json_decode($response, true),
        ];
    }


    /**
     * Display the specified resource.
     */
    public function show(contrevention $contrevention)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(contrevention $contrevention)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatecontreventionRequest $request, contrevention $contrevention)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(contrevention $contrevention)
    {
        //
    }
}
