<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class LayawayController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required',
            'customer_email' => 'required',
            'contact_number' => 'required',
        ]);

        $customer = Customer::where('email', $request->customer_email)->orWhere('phone_number', $request->contact_number)->first();
        if(!$customer) {
            Customer::create([
                'name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone_number' => $request->contact_number
            ]);
        }

        return response()->json([
            'customer_id' => $customer->id,
        ]);
    }
}
