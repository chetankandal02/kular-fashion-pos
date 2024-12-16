<?php

namespace App\Http\Controllers;

use App\Models\GiftVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiftVoucherController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:50',
            'auth_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $giftVoucher = GiftVoucher::create([
            'amount' => $request->amount,
            'payment_through' => $request->payment_method,
            'generated_by' => $request->auth_user_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gift voucher generated successfully!',
            'gift_voucher' => $giftVoucher
        ], 201);
    }

    public function apply(Request $request){
        $giftVoucher = GiftVoucher::where('barcode', $request->barcode)->whereNull('deleted_at')->first();
        if(!$giftVoucher){
            return response()->json([
                'success' => false,
                'message' => 'Invalid gift voucher',
            ]);
        }

        $giftVoucher->delete();

        return response()->json([
            'success' => true,
            'gift' => $giftVoucher,
        ]);
    }
}
