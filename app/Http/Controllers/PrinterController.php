<?php

namespace App\Http\Controllers;
use App\Services\ReceiptService;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class PrinterController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function printTestReceipt()
    {
        $this->receiptService->testPrint();
        return response()->json(['success' => true]);
    }

    public function printEod(Request $request)
    {
        $salesPersonId = $request->salesPersonId;
        $this->receiptService->printEod($salesPersonId);

        return response()->json(['success' => true]);
    }
}
