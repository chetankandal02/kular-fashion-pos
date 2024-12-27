<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Picqer\Barcode\BarcodeGeneratorPNG;

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

    public function printLogo(){
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
        $lineWidth = 48;
        $this->printer->text(str_repeat($character, $lineWidth) . "\n");
    }

    public function printLetterHead(){
        $this->printer->text("Kular Fashion\n");
        $this->printer->text("19-21 Ferryquary Street\n");
        $this->printer->text("Tel. 028 7126 1326\n");
        $this->printer->text("www.kularfashion.com\n");
    }

    public function formatMoney($amount){
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
        $this->printer->text(str_pad('         Date', 30) . date('d/m/Y')."\n");
        $this->printer->text(str_pad('         Time', 30) . date('H:i:s')."\n");
        $this->printer->text(str_pad('         Value', 30) . $this->formatMoney($giftVoucher['amount'])."\n");
        $this->printer->text(str_pad('         Authorized By:', 30) . "\n\n\n\n");
        $this->printFullWidthLine('.');

        $this->printer->cut();
        $this->printer->close();
    }

    public function printCreditNote($creditNote){
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
        $this->printer->text(str_pad('         Date', 30) . date('d/m/Y')."\n");
        $this->printer->text(str_pad('         Time', 30) . date('H:i:s')."\n");
        $this->printer->text(str_pad('         Value', 30) . $this->formatMoney($creditNote['amount'])."\n");
        $this->printer->text(str_pad('         Authorized By:', 30) . "\n\n\n\n");
        $this->printFullWidthLine('.');
        $this->printer->cut();
        $this->printer->close();
    }
}
