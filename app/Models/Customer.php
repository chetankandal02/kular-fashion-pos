<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate and set the customer code before creating the customer
        static::creating(function ($customer) {
            $customer->code = static::generateCustomerCode();
        });
    }

    /**
     * Generate an auto-incremented customer code.
     *
     * @return string
     */
    protected static function generateCustomerCode()
    {
        $latestCustomer = static::latest('id')->first();
        $nextId = $latestCustomer ? $latestCustomer->id + 1 : 1;
        $formattedId = str_pad($nextId, 6, '0', STR_PAD_LEFT);
        $prefix = 'CUST-';
        return $prefix . $formattedId;
    }
}
