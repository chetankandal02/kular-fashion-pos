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
            'customer_name' => 'required',
            'customer_email' => 'required',
            'contact_number' => 'required',
        ]);

        $customer = Customer::where('email', $request->customer_email)->orWhere('phone_number', $request->contact_number)->first();
        if(!$customer) {
           $customer =  Customer::create([
                'name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone_number' => $request->contact_number
            ]);

            $layawayCode = $this->generatelayawayCode();
            $layaway = Layaway::create([
                'layaway_code' => $layawayCode,
                'customer_id' => $customer->id,
                'order_id'    => 1
            ]); 

            if($layaway) {
                LayawayPayment::create([
                    'layaway_id'   => $layaway->id,
                    'customer_id' => $customer->id,
                    'balance'     => $request->grand_total - $request->down_payment,
                    'amount_paid' => $request->down_payment
                ]);
            }

            return response()->json([
                'success' => true,
                'customer_id' => $customer->id,
            ]);
        } else {
            return response()->json([
                'success' =>false,
            ]);
        }
    }

    private function generatelayawayCode()
    {
        $lastlayaway = Layaway::orderBy('layaway_code', 'desc')->first();

        if (!$lastlayaway) {
            return 'LW-000001';
        }

        preg_match('/S-25-(\d+)/', $lastlayaway->layaway_code, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT); // Pad the number to 6 digits

        return 'LW-' . $newNumber;
    }
}
