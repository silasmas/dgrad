<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use App\Models\transaction;
use Illuminate\Http\Request;
use App\Models\contreventionUser;
use App\Http\Requests\StoretransactionRequest;
use App\Http\Requests\UpdatetransactionRequest;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function checkTransactionStatus(Request $request)
    {
        $reference = $request->input('reference');

        // Construire l'URL avec le paramètre de requête
        $url = 'https://backend.flexpay.cd/api/rest/v1/check/' . urlencode($reference);

        $curl = curl_init($url);

        // Définir les options de cURL pour GET
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . env('FLEXPAY_API_TOKEN'),
        ]);

        // Exécuter la requête
        $curlResponse = curl_exec($curl);

        // Gérer les erreurs de cURL
        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            \Log::error("Erreur cURL : " . $errorMessage);
            return response()->json(['error' => 'Erreur de connexion au service FlexPay'], 500);
        }

        curl_close($curl);

        // Valider et traiter la réponse JSON
        $jsonRes = json_decode($curlResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error("Erreur de décodage JSON : " . json_last_error_msg());
            return response()->json(['error' => 'Réponse invalide du service FlexPay'], 500);
        }

        // Vérifiez que la réponse contient les données attendues
        if (!isset($jsonRes['transaction']) || !is_array($jsonRes['transaction'])) {
            \Log::error('Structure inattendue dans la réponse FlexPay', $jsonRes);
            return response()->json(['error' => 'Réponse mal formatée du service FlexPay'], 500);
        }

        // Journaliser la réponse pour le débogage
        \Log::info('Réponse FlexPay reçue : ', $jsonRes);
        if ($jsonRes["code"] != 0) {
            return response()->json([
                'reponse' => false,
                'message' => $jsonRes["message"],
            ]);
        } else {
            $transactionData = $jsonRes['transaction'];
            // Trouver la transaction correspondante
            $transaction = Transaction::where('reference', $transactionData['reference'])->first();

            if ($transaction) {
                $contrevention = contreventionUser::where('reference', $transactionData['reference'])->first();

                // Mettre à jour l'état des données
                $etat = $transactionData['status'] == 0 ? '1' : '0';
                if ($contrevention) {

                    $contrevention->update([
                        'etat' => $etat,
                        'updated_at' => now()
                    ]);

                    $transaction->update([
                        'etat' => $etat,
                        'updated_at' => now()
                    ]);
                    $message = new ContreventionController();
                    $user = User::where("matricule", $contrevention->matricule)->first();
                    $messages = $user->fisrtname . " " . $user->name . " votre contrevention de reference " . $contrevention->reference . " à été soldée !";

                    $message->sendSms($user->phone, $messages);
                    return response()->json([
                        'reponse' => $etat == '1' ? true : false,
                        'message' => $jsonRes['message'] ?? 'Statut de transaction mis à jour avec succès.',
                        'status' => $transactionData['status'],
                    ]);
                } else {
                    return response()->json([
                        'reponse' => false,
                        'message' => 'Contrevention non trouvée.',
                        'status' => $transactionData['status'],
                    ]);
                }
            } else {
                return response()->json([
                    'reponse' => false,
                    'message' => 'Transaction non trouvée.',
                    'status' => $transactionData['status'],
                ]);
            }
        }
        // dd($jsonRes["transaction"]);
    }


    // public function checkTransactionStatus(Request $request)
    // {
    //     $reference = $request->input('reference');

    //     // Construire l'URL avec le paramètre de requête
    //     $url = 'https://backend.flexpay.cd/api/rest/v1/check/' . urlencode($reference);
    //     // $url = env('FLEXPAY_GATEWAY_CHECK') . '?orderNumber=' . urlencode($reference);

    //     $curl = curl_init($url);

    //     // Définir les options de cURL pour GET
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($curl, CURLOPT_HTTPHEADER, [
    //         'Authorization: Bearer ' . env('FLEXPAY_API_TOKEN'),
    //     ]);

    //     // Exécuter la requête
    //     $curlResponse = curl_exec($curl);

    //     // Gérer les erreurs de cURL
    //     if (curl_errno($curl)) {
    //         $errorMessage = curl_error($curl);
    //         \Log::error("Erreur cURL : " . $errorMessage);
    //         return response()->json(['error' => 'Erreur de connexion au service FlexPay'], 500);
    //     }

    //     curl_close($curl);

    //     // Valider et traiter la réponse
    //     $jsonRes = json_decode($curlResponse, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         \Log::error("Erreur de décodage JSON : " . json_last_error_msg());
    //         return response()->json(['error' => 'Réponse invalide du service FlexPay'], 500);
    //     }

    //     // Enregistrer la réponse pour le débogage
    //     \Log::info('Réponse FlexPay reçue : ', $jsonRes[2]);



    //     dd($jsonRes);
    //     // $data = [
    //     //     'timestamp' => now(),
    //     //     'reference' => $request->input('reference'),
    //     // ];

    //     // \Log::info('détails check: ', $data);
    //     // $reference = $request->query('reference');

    //     $transaction = Transaction::where('reference', $jsonRes->transaction->reference)->first();

    //     if ($transaction) {
    //         $contrevention = contreventionUser::where('reference', $jsonRes->transaction->reference)->first();

    //         $contrevention->update([
    //             'etat' => $jsonRes->transaction->status === 0 ? '1' : '0',
    //             'updated_at' => now()
    //         ]);
    //         $transaction->update([
    //             'etat' => $jsonRes->transaction->status === 0 ? '1' : '0',
    //             'updated_at' => now()
    //         ]);
    //         return response()->json([
    //             'reponse' => $jsonRes->transaction->status === 0 ? true : false,
    //             'message' => $jsonRes->message,
    //             'status' => $jsonRes->transaction->status,
    //         ]);
    //     }

    //     return response()->json([
    //         'reponse' => false,
    //         'message' => 'Transaction non trouvée.',
    //     ]);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Callback reçu : ', "retour callback");
        $data = [
            'timestamp' => now(),
            'reference' => $request->input('reference'),
            'status' => $request->input('status'),
        ];

        \Log::info('Callback reçu avec détails : ', $data);
        $data = $request->all();

        // Log des données pour le débogage
        Log::info('Callback reçu : ', $data);

        // $data = file_get_contents('php://input');
        // $json = json_decode($request, true);
        // $reference = $json['reference'];
        // $code = $json['code'];
        // $order_number = $json['order_number'];
        // $createdAt = $json['createdAt'];
        // $channel = $json['channel'];
        // $amount = $json['amount'];
        // $currency = $json['currency'];
        // $status = $json['status'];
        // $message = $json['message'];

        // $myfile = fopen("callback.txt", "w") or die("Unable to open file!"); // Fichier Callback.txt à créer

        // fwrite($myfile, "Reference : " . $reference . "\n");
        // fwrite($myfile, "Code : " . $code . "\n");
        // fwrite($myfile, "Order number : " . $order_number . "\n");
        // fwrite($myfile, "Date requête : " . $createdAt . "\n");
        // fwrite($myfile, "Canal paiement : " . $channel . "\n");
        // fwrite($myfile, "Montant payé : " . $amount . "\n");
        // fwrite($myfile, "Devise paiement : " . $currency . "\n");
        // fwrite($myfile, "Statut transaction : " . $status . "\n");
        // fwrite($myfile, "Message : " . $message . "\n");

        // fclose($myfile);

        // $user_id = is_numeric(explode('-', $request->reference)[2]) ? (int) explode('-', $request->reference)[2] : null;
        // $cart_id = is_numeric(explode('-', $request->reference)[3]) ? (int) explode('-', $request->reference)[3] : null;
        // $donation_id = is_numeric(explode('-', $request->reference)[4]) ? (int) explode('-', $request->reference)[4] : null;
        // Check if payment already exists
        $payment = transaction::where('reference', operator: $request->reference)->first();

        // If payment exists
        if ($payment != null) {
            $contre = contreventionUser::where('reference', $request->reference)->first();

            $contre->update([
                'etat' => $request->code === 0 ? '1' : '0',
                'updated_at' => now()
            ]);
            $payment->update([
                'reference' => $request->reference,
                'provider_reference' => $request->provider_reference,
                'order_number' => $request->order_number,
                'amount' => $request->amount,
                'amount_customer' => $request->amountCustomer,
                'phone' => $request->phone,
                'currency' => $request->currency,
                'chanel' => $request->channel,
                'type_id' => $request->type,
                'etat' => $request->code,
                'updated_at' => now()
            ]);
            return response()->json(
                [
                    'reponse' => true,
                    'msg' => 'Le paiement effectué'
                ]
            );
            // Otherwise, create new payment
            // } else {

            //     $payment = transaction::create([
            //         'reference' => $request->reference,
            //         'provider_reference' => $request->provider_reference,
            //         'order_number' => $request->orderNumber,
            //         'amount' => $request->amount,
            //         'amount_customer' => $request->amountCustomer,
            //         'phone' => $request->phone,
            //         'currency' => $request->currency,
            //         'chanel' => $request->channel,
            //         'created_at' => $request->createdAt,
            //         'type_id' => $request->type,
            //         'etat' => $request->code
            //     ]);
            //     return response()->json(
            //         [
            //             'reponse' => true,
            //             'msg' => 'Le paiement effectué'
            //         ]
            //     );
        } else {
        }
    }
    public function findByPhone($phone_number)
    {
        $payments = transaction::where('phone', $phone_number)->get();

        return response()->json(
            [
                'reponse' => true,
                'msg' => 'Le paiement effectué',
                'data' =>  $payments

            ]
        );
    }

    /**
     * find payment by order number.
     *
     * @param  string $order_number
     * @param  string $user_id
     * @return \Illuminate\Http\Response
     */
    public function findByOrderNumber($order_number)
    {
        $payment = transaction::where('order_number', $order_number)->first();

        if (is_null($payment)) {
            return response()->json(
                [
                    'reponse' => false,
                    'msg' => 'Ce paiement n\'existe pas!',
                    'data' => $payment,

                ]
            );
        }

        return response()->json(
            [
                'reponse' => true,
                'msg' => 'Le paiement effectué',
                'data' =>  $payment

            ]
        );
    }
    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatetransactionRequest $request, transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(transaction $transaction)
    {
        //
    }
}
