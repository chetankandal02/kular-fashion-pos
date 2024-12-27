<?php

namespace App\Http\Controllers;
use App\Services\ReceiptService;

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
}
