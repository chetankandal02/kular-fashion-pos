<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function sentTransfers()
    {
        return $this->hasMany(InventoryTransfer::class, 'sent_from');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(InventoryTransfer::class, 'sent_to');
    }

    public function inventory()
    {
        return $this->hasMany(StoreInventory::class, 'store_id');
    }
}
