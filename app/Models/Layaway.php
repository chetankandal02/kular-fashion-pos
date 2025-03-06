<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layaway extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    
    protected static function booted()
    {
        static::creating(function ($layaway) {
            $lastLayaway = Layaway::where('code', 'like', 'LW-%')
                ->orderByDesc('id')
                ->first();

            // If a previous code exists, increment the numeric part
            if ($lastLayaway) {
                $lastCode = $lastLayaway->code;
                $number = (int) substr($lastCode, 3);
                $newNumber = str_pad($number + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '000001';
            }

            $layaway->code = 'LW-' . $newNumber;
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(LayawayPayment::class);
    }
}
