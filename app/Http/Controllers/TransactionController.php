<?php

namespace App\Http\Controllers;

use App\Models\transaction;
use Illuminate\Http\Request;
use App\Http\Requests\StoretransactionRequest;
use App\Http\Requests\UpdatetransactionRequest;
use App\Models\contreventionUser;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_id = is_numeric(explode('-', $request->reference)[2]) ? (int) explode('-', $request->reference)[2] : null;
        $cart_id = is_numeric(explode('-', $request->reference)[3]) ? (int) explode('-', $request->reference)[3] : null;
        $donation_id = is_numeric(explode('-', $request->reference)[4]) ? (int) explode('-', $request->reference)[4] : null;
        // Check if payment already exists
        $payment = transaction::where('order_number', $request->orderNumber)->first();
        
        // If payment exists
        if ($payment != null) {
            $contre = contreventionUser::where('reference', $request->reference)->first();

            $contre->update([
                'etat' => $request->code===0?'1':'0',
                'updated_at' => now()
            ]);
            $payment->update([
                'reference' => $request->reference,
                'provider_reference' => $request->provider_reference,
                'order_number' => $request->orderNumber,
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
        } else {

            $payment = transaction::create([
                'reference' => $request->reference,
                'provider_reference' => $request->provider_reference,
                'order_number' => $request->orderNumber,
                'amount' => $request->amount,
                'amount_customer' => $request->amountCustomer,
                'phone' => $request->phone,
                'currency' => $request->currency,
                'chanel' => $request->channel,
                'created_at' => $request->createdAt,
                'type_id' => $request->type,
                'etat' => $request->code
            ]);
            return response()->json(
                [
                    'reponse' => true,
                    'msg' => 'Le paiement effectué'
                ]
            );
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
