<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use Illuminate\Http\Request;
use App\Services\ReceiptService;

class CreditNoteController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function create(Request $request)
    {
        $creditNote = CreditNote::create([
            'amount' => abs($request->input('amount')),
            'generated_by' => $request->auth_user_id
        ]);

        try {
            $this->receiptService->printCreditNote($creditNote->toArray());
            return response()->json([
                'success' => true,
                'message' => 'Credit note generated successfully!',
                'credit_note' => $creditNote
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print credit note!',
            ], 200);
        }
    }

    public function apply(Request $request)
    {
        $creditNote = CreditNote::where('barcode', $request->barcode)->whereNull('deleted_at')->first();
        if (!$creditNote) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credit note',
            ]);
        }

        $creditNote->delete();

        return response()->json([
            'success' => true,
            'credit' => $creditNote,
        ]);
    }
}
