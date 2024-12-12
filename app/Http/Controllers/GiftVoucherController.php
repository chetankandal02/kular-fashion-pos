<?php

namespace App\Http\Controllers;

use App\Models\GiftVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiftVoucherController extends Controller
{
    public function create(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:50',
            'auth_user_id' => 'required',
        ]);

        // If validation fails, return the error response
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        // Create the gift voucher record with the generated_by ID
        $giftVoucher = GiftVoucher::create([
            'amount' => $request->amount,
            'payment_through' => $request->payment_method,
            'generated_by' => $request->auth_user_id
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Gift voucher generated successfully!',
            'gift_voucher' => $giftVoucher
        ], 201);
    }
}
