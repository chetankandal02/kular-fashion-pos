<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Models\Brand;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\GiftVoucher;
use App\Models\OrderPayment;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    protected $printer;

    public function __construct()
    {
        $connector = new WindowsPrintConnector('KULAR-POS');
        $this->printer = new Printer($connector);
    }

    public function testPrint()
    {
        $this->printLogo();
        $this->printer->text("Printer successfully configured\n");
        // Cut the paper and close the printer
        $this->printer->cut();
        $this->printer->close();
    }

    public function printLogo()
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $imagePath = public_path('assets/images/posLogo.png');
        $image = EscposImage::load($imagePath);
        $this->printer->bitImage($image);
    }

    public function printBarcode($barcode)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeImage = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 3, 100);
        file_put_contents('barcode.png', $barcodeImage);

        $imagePath = public_path('barcode.png');
        $image = EscposImage::load($imagePath);
        $this->printer->bitImage($image);
    }

    public function printFullWidthLine($character = '=')
    {
        $lineWidth = 40;
        $this->printer->text(str_repeat($character, $lineWidth) . "\n");
    }

    public function printLetterHead()
    {
        $this->printer->text("Kular Fashion\n");
        $this->printer->text("19-21 Ferryquary Street\n");
        $this->printer->text("Tel. 028 7126 1326\n");
        $this->printer->text("www.kularfashion.com\n");
    }

    public function formatMoney($amount)
    {
        return '£' . number_format($amount, 2);
    }

    public function printGiftVoucher($giftVoucher)
    {
        $barcode = $giftVoucher['barcode'];

        $this->printLogo();
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printLetterHead();

        $this->printFullWidthLine('=');
        $this->printer->setEmphasis(true);
        $this->printer->text("GIFT VOUCHER\n");
        $this->printer->text($barcode . "\n");
        $this->printBarcode($barcode);
        $this->printer->setEmphasis(false);
        $this->printFullWidthLine('=');

        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_pad('         Date', 30) . date('d/m/Y') . "\n");
        $this->printer->text(str_pad('         Time', 30) . date('H:i:s') . "\n");
        $this->printer->text(str_pad('         Value', 30) . $this->formatMoney($giftVoucher['amount']) . "\n");
        $this->printer->text(str_pad('         Authorized By:', 30) . "\n\n\n\n");
        $this->printFullWidthLine('.');

        $this->printer->cut();
        $this->printer->close();
    }

    public function printCreditNote($creditNote)
    {
        $barcode = $creditNote['barcode'];

        $this->printLogo();
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->printFullWidthLine('=');
        $this->printer->setEmphasis(true);
        $this->printer->text("CREDIT NOTE\n");
        $this->printer->text($barcode . "\n");
        $this->printBarcode($barcode);
        $this->printer->setEmphasis(false);
        $this->printFullWidthLine('=');

        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_pad('         Date', 30) . date('d/m/Y') . "\n");
        $this->printer->text(str_pad('         Time', 30) . date('H:i:s') . "\n");
        $this->printer->text(str_pad('         Value', 30) . $this->formatMoney($creditNote['amount']) . "\n");
        $this->printer->text(str_pad('         Authorized By:', 30) . "\n\n\n\n");
        $this->printFullWidthLine('.');
        $this->printer->cut();
        $this->printer->close();
    }

    public function printOrderReceipt($orderId, $isDuplicate = false)
    {
        $order = Order::with('orderItems', 'paymentMethods')->find($orderId);
        $groupedItems = $order->orderItems->groupBy('flag');

        if ($order) {
            $branch = Branch::find($order->branch_id);
            $this->printLogo();
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            if ($branch->location) {
                $this->printer->text($branch->location . "\n");
            }
            $orderReceiptFooter = $branch->order_receipt_footer ?? setting("order_receipt_footer");
            $orderReceiptHeader = $branch->order_receipt_header ?? setting("order_receipt_header");
            $this->printer->text($orderReceiptHeader . "\n");

            if ($branch->contact) {
                $this->printer->text("Tel. " . $branch->contact . "\n");
            }
            $this->printer->text("www.kularfashion.com\n");

            if ($isDuplicate) {
                $this->printFullWidthLine('-');
                $this->printer->setEmphasis(true);
                $this->printer->text("Duplicate\n");
                $this->printer->setEmphasis(false);
                $this->printFullWidthLine('-');
            }

            foreach ($groupedItems as $saleType => $group) {
                $this->printer->text("\n" . $saleType . " ITEMS\n");
                $this->printer->setJustification(Printer::JUSTIFY_LEFT);
                foreach ($group as $item) {
                    $this->printOrderItem($item);
                }
                $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            }

            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->setEmphasis(true);
            $this->printer->text(str_pad('Total Goods', 30) . '£' . $order->total_payable_amount    . "\n");
            $this->printer->text("Tender\n");
            $this->printer->setEmphasis(false);

            $this->printPaymentMethods($order->paymentMethods);

            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            if ($isDuplicate) {
                $this->printFullWidthLine('-');
                $this->printer->setEmphasis(true);
                $this->printer->text("Duplicate\n");
                $this->printer->setEmphasis(false);
                $this->printFullWidthLine('-');
            }
            $this->printer->text($orderReceiptFooter . "\n");
            $this->printer->cut();
            $this->printer->close();
        } else {
            return ['success' => false, 'message' => 'Order not found'];
        }
    }


    public function printGiftReceipt($orderId, $isDuplicate = false)
    {
        $order = Order::with('orderItems', 'paymentMethods')->find($orderId);
        $groupedItems = $order->orderItems->groupBy('flag');

        if ($order) {
            $branch = Branch::find($order->branch_id);
            $this->printLogo();
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            if ($branch->location) {
                $this->printer->text($branch->location . "\n");
            }
            $orderReceiptFooter = $branch->order_receipt_footer ?? setting("order_receipt_footer");
            $orderReceiptHeader = $branch->order_receipt_header ?? setting("order_receipt_header");
            $this->printer->text($orderReceiptHeader . "\n");

            if ($branch->contact) {
                $this->printer->text("Tel. " . $branch->contact . "\n");
            }
            $this->printer->text("www.kularfashion.com\n");

            if ($isDuplicate) {
                $this->printFullWidthLine('-');
                $this->printer->setEmphasis(true);
                $this->printer->text("Duplicate\n");
                $this->printer->setEmphasis(false);
                $this->printFullWidthLine('-');
            }

            foreach ($groupedItems as $saleType => $group) {
                $this->printer->text("\n" . $saleType . " ITEMS\n");
                $this->printer->setJustification(Printer::JUSTIFY_LEFT);
                foreach ($group as $item) {
                    $this->printGiftItem($item);
                    // dd($item);
                }
                // $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            }

            // $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            // $this->printer->setEmphasis(true);
            // $this->printer->text(str_pad('Total Goods', 30) . '£'.$order->total_payable_amount	."\n");
            // $this->printer->text("Tender\n");
            // $this->printer->setEmphasis(false);

            // $this->printPaymentMethods($order->paymentMethods);

            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            if ($isDuplicate) {
                $this->printFullWidthLine('-');
                $this->printer->setEmphasis(true);
                $this->printer->text("Duplicate\n");
                $this->printer->setEmphasis(false);
                $this->printFullWidthLine('-');
            }
            $this->printer->text($orderReceiptFooter . "\n");
            $this->printer->cut();
            $this->printer->close();
        } else {
            return ['success' => false, 'message' => 'Order not found'];
        }
    }

    protected function printOrderItem($item)
    {
        $product = Product::find($item->product_id);
        $brand = Brand::find($item->brand_id);
        $this->printer->text(str_pad($item->barcode . ' * 1', 30) . '£' . $item->changed_price . "\n");
        $this->printer->text($product->article_code . '      ' . $item->color_name . '      ' . $item->size . '      ' . ($brand ? $brand->short_name : $item->brand_name) . "\n");
        $this->printer->text($product->short_description . "\n\n");
    }

    protected function printGiftItem($item)
    {
        $product = Product::find($item->product_id);
        $brand = Brand::find($item->brand_id);
        $this->printer->text(str_pad($item->barcode . ' * 1', 30) . "\n");
        $this->printer->text($product->article_code . '      ' . $item->color_name . '      ' . $item->size . '      ' . ($brand ? $brand->short_name : $item->brand_name) . "\n");
        $this->printer->text($product->short_description . "\n\n");
    }

    protected function printPaymentMethods($paymentMethods)
    {
        foreach ($paymentMethods as $method) {
            $this->printer->text(str_pad($method->method, 30) . '£' . $method->amount . "\n");
        }
    }

    public function printEod($salesPersonId)
    {
        $user = User::with('branch')->where('id', $salesPersonId)->first();
        $storeName = $user->branch->name;
        $orderReceiptHeader = $user->branch->order_receipt_header ?? setting("order_receipt_header");
        $date = Carbon::now();
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $salesData = OrderItem::where('sales_person_id', $salesPersonId)->whereDate('created_at', $date)->get();
        $saleItems =  $salesData->where('flag', 'SALE')->count();
        $saleReturns = $salesData->where('flag', 'RETURN')->count();
        $miscSales = number_format($salesData->where('flag', 'SALE')->sum('changed_price'), 2);
        $miscReturns = number_format($salesData->where('flag', 'RETURN')->sum('changed_price'), 2);
        $giftVoucherSold = GiftVoucher::where('generated_by', $salesPersonId)->whereDate('created_at', $date)->get();
        $creditNotesIssue = DB::table('credit_notes')->where('generated_by', $salesPersonId)->whereDate('created_at', $date)->whereNull('deleted_at')->get();
        $giftVoucherRedeemeds = OrderPayment::whereDate('created_at', $date)
            ->whereHas('order', function ($query) use ($salesPersonId) {
                $query->where('sales_person_id', $salesPersonId);
            })->get();
        $giftVouchersSold = $giftVoucherSold->count();
        $giftVouchersRedeemed = number_format($giftVoucherRedeemeds->where('method', '!=', 'Credit Note')->sum('original_amount'), 2);
        $creditNotesIssued = $creditNotesIssue->count();
        $creditNotesRedeemed = number_format($giftVoucherRedeemeds->where('method', 'Credit Note')->sum('original_amount'), 2);

        $this->printLogo();
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->printFullWidthLine('=');
        $this->printer->text($storeName . "\n");
        $this->printer->text($orderReceiptHeader . "\n");
        $this->printer->text('Phone Number: 445454' . "\n");
        $this->printer->text('Email: asdas@sds.dd' . "\n");
        $this->printer->text($today . "\n");
        // $this->printBarcode($storeName);
        $this->printFullWidthLine('=');
        $this->printer->setEmphasis(true);
        $this->printer->text("SALE OF GOODS\n");
        $this->printer->setEmphasis(false);

        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_pad('       Sale Items', 35) . $saleItems . "\n");
        $this->printer->text(str_pad('       Sale Return Items', 35) . $saleReturns . "\n");
        $this->printer->text(str_pad('       Misc Sales', 35) . '£' . $miscSales . "\n");
        $this->printer->text(str_pad('       Misc Returns', 35) . '£' . $miscReturns . "\n");
        $this->printFullWidthLine('=');

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setEmphasis(true);
        $this->printer->text("Vouchers Exchange\n");
        $this->printer->setEmphasis(false);
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_pad('       Gift Vouchers Sold', 35) . $giftVouchersSold . "\n");
        $this->printer->text(str_pad('       Gift Vouchers Redeemed', 35) . '£' . $giftVouchersRedeemed . "\n");
        $this->printer->text(str_pad('       Credit Notes Issued', 35) . $creditNotesIssued . "\n");
        $this->printer->text(str_pad('       Credit Notes Redeemed', 35) . '£' . $creditNotesRedeemed . "\n\n\n");

        $this->printFullWidthLine('.');
        $this->printer->cut();
        $this->printer->close();
    }
}
