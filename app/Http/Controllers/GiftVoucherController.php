<?php

namespace App\Http\Controllers;

use App\Models\GiftVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ReceiptService;

class GiftVoucherController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

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

        try {
            $this->receiptService->printGiftVoucher($giftVoucher->toArray());
            return response()->json([
                'success' => true,
                'message' => 'Gift voucher generated successfully!',
                'gift_voucher' => $giftVoucher
            ], 201);
         } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print gift voucher!',
            ], 200);
        }
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
