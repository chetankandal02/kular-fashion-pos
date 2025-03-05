<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Layaway;
use App\Models\LayawayPayment;

class LayawayController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customerId' => 'required',
        ]);

        dd($request->all());
        $customer = Customer::find($request->customerId);

        $layaway = Layaway::create([
            'customer_id' => $customer->id,
            'order_id'    => 1
        ]); 
    }
}
