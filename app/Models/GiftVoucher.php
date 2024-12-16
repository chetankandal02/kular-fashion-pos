<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiftVoucher extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($giftVoucher) {
            $lastBarcode = GiftVoucher::where('barcode', 'like', '100%')
                ->orderBy('barcode', 'desc')
                ->value('barcode');

            if ($lastBarcode) {
                $lastNumericPart = substr($lastBarcode, 0, 6);
                $newNumericPart = str_pad($lastNumericPart + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $newNumericPart = '100000';
            }

            $datePart = Carbon::now()->format('dmy');

            $barcodeWithoutCheckDigit = $newNumericPart . $datePart;
            $checkDigit = calculateCheckDigit($barcodeWithoutCheckDigit);

            $giftVoucher->barcode = $barcodeWithoutCheckDigit . $checkDigit;
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
