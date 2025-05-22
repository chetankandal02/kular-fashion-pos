<?php

namespace App\Http\Controllers;

use App\Models\GiftVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ReceiptService;
use Illuminate\Support\Facades\DB;

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
        if ($request->payment_method == 'Euro') {
            $exchangeRate = setting("euro_to_pound");
            $amount = (float) $request->amount;
            $amount = $amount * $exchangeRate;
        } else {
            $amount = $request->amount;
        }

        $giftVoucher = GiftVoucher::create([
            'amount' => $amount,
            'payment_through' => $request->payment_method,
            'generated_by' => $request->auth_user_id
        ]);

        try {
            $this->receiptService->printGiftVoucher($giftVoucher->toArray());
            return response()->json([
                'success' => true,
                'message' => 'Gift voucher generated successfully!',
                'gift_voucher' => $giftVoucher,
                'amount' => $amount
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print gift voucher!',
            ], 200);
        }
    }

    public function apply(Request $request)
    {
        $giftVoucher = GiftVoucher::where('barcode', $request->barcode)->whereNull('deleted_at')->first();
        if (!$giftVoucher) {
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

    public function getGiftVoucherOrders(Request $request)
    {
        $query = DB::table('gift_vouchers') // or your model
            ->select('id', 'barcode', 'payment_through', 'amount', 'generated_by', 'created_at');

        // Apply filters if any
        if ($request->filled('voucher_code')) {
            $query->where('barcode', 'like', '%' . $request->voucher_code . '%');
        }

        if ($request->filled('voucher_start_date')) {
            $query->whereDate('created_at', '>=', $request->voucher_start_date);
        }

        if ($request->filled('voucher_end_date')) {
            $query->whereDate('created_at', '<=', $request->voucher_end_date);
        }

        $total = $query->count();

        // Pagination for DataTables
        $data = $query
            ->offset($request->start)
            ->limit($request->length)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ]);
    }
}
