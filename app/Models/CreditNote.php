<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($creditNote) {
            $lastBarcode = CreditNote::where('barcode', 'like', '600%')
                ->orderBy('barcode', 'desc')
                ->value('barcode');

            if ($lastBarcode) {
                $lastNumericPart = substr($lastBarcode, 0, 6);
                $newNumericPart = str_pad($lastNumericPart + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $newNumericPart = '600000';
            }

            $datePart = Carbon::now()->format('dmy');
            $barcodeWithoutCheckDigit = $newNumericPart . $datePart;
            $checkDigit = calculateCheckDigit($barcodeWithoutCheckDigit);

            $creditNote->barcode = $barcodeWithoutCheckDigit . $checkDigit;
        });

        
        function calculateCheckDigit($barcode) {
            $sum = 0;
            $reverseBarcode = strrev($barcode);
        
            for ($i = 0; $i < strlen($reverseBarcode); $i++) {
                $digit = (int) $reverseBarcode[$i];
        
                if ($i % 2 == 1) {
                    $digit *= 2;
        
                    if ($digit > 9) {
                        $digit -= 9;
                    }
                }
        
                $sum += $digit;
            }
        
            return (10 - ($sum % 10)) % 10;
        }
    }
}
