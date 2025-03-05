<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Layaway;
use App\Models\LayawayPayment;
use App\Models\Order;

class LayawayController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'orderId' => 'required',
            'customerId' => 'required',
        ]);

        $order = Order::find($request->orderId);
        if(!$order){
            return response()->json([
                'success' => false,
                'message' => 'Order not found!',
            ], 200);  
        }

        $pendingBalance = $order->total_payable_amount - $order->paid_amount;

        $customer = Customer::find($request->customerId);
        $customer->balance += $pendingBalance;
        $customer->save();

        if(!$customer){
            return response()->json([
                'success' => false,
                'message' => 'Customer not found!',
            ], 200);  
        }


        $layaway = Layaway::create([
            'customer_id'       => $request->customerId,
            'order_id'          => $request->orderId,
            'total_amount'      => $order->total_payable_amount,
            'paid_amount'       => $order->paid_amount,
            'balance'           => $pendingBalance,
            'sales_person_id'   => $order->sales_person_id,
            'note'              => $request->note,
        ]); 

        $paymentMethods = $request->input('paymentMethods', []);
        $exchangeRate = setting("euro_to_pound");

        foreach($paymentMethods as $paymentMethod){
            if ($paymentMethod['method'] === 'Euro') {
                $convertedAmount = (float) $paymentMethod['amount'] * $exchangeRate;
            }

            LayawayPayment::create([
                'layaway_id' => $layaway->id,
                'customer_id' => $request->customerId,
                'method' => $paymentMethod['method'],
                'amount' => $paymentMethod['amount'],
                'original_amount' => $convertedAmount ?? $paymentMethod['amount'],
                'sales_person_id' => $order->sales_person_id,
                'payment_date' => now(),
            ]);    
        }

        return response()->json([
            'success' => true,
            'message' => 'Layaway processed successfully!',
        ], 201);
    }
}
