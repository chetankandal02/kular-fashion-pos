<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GiftVoucher extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($giftVoucher) {
            // Get the last barcode from the database, if it exists
            $lastBarcode = GiftVoucher::where('barcode', 'like', '100%')  // Ensure we're looking at the relevant barcode format
                ->orderBy('barcode', 'desc')
                ->value('barcode');

            // Increment the number part of the barcode
            if ($lastBarcode) {
                // Extract the numeric part (first 6 digits) from the last barcode
                $lastNumericPart = substr($lastBarcode, 0, 6);
                $newNumericPart = str_pad($lastNumericPart + 1, 6, '0', STR_PAD_LEFT);  // Ensure it's 6 digits
            } else {
                // If no previous barcode exists, start from 100000
                $newNumericPart = '100000';
            }

            // Format the current date in DDMMYY
            $datePart = Carbon::now()->format('dmy');

            // Combine both parts to create the final barcode
            $giftVoucher->barcode = $newNumericPart . $datePart;
        });
    }
}
