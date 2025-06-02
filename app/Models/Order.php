<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($order) {
            $lastOrder = self::orderBy('id', 'desc')->first();
            $nextOrderNumber = $lastOrder ? (int) substr($lastOrder->code, -5) + 1 : 1;

            $order->code = 'ODR-' . str_pad($nextOrderNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function salesPerson()
    {
        return $this->belongsTo(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function layaway()
    {
        return $this->hasOne(Layaway::class, 'order_id');
    }
}
