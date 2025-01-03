<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ProductQuantity;
use App\Models\InventoryTransfer;
use App\Models\InventoryItem;
use App\Models\StoreInventory;

use Illuminate\Http\Request;

class InventoryTransferController extends Controller
{
    public function InventoryTransferItems(Request $request)
    {
        $fromStoreId = $request->fromStore;
        $toStoreId = $request->toStore;
        $items = $request->items;

        $inventory = InventoryTransfer::create([
            'sent_from'     => $fromStoreId,
            'sent_to'       => $toStoreId,
            'sent_by'       => Auth::id()
        ]);

        foreach ($items as $value) {
            $productQuantityId = $value['product_quantity_id'];
            $quantity = $value['quantity'];

            $productQuantity = ProductQuantity::find($productQuantityId);
            if ($toStoreId > 1) {
                $productQuantity->quantity = $productQuantity->quantity - $quantity;
            } else {
                $productQuantity->quantity = $productQuantity->quantity + $quantity;
            }
            $productQuantity->save();

            InventoryItem::create([
                'inventroy_transfer_id' => $inventory->id,
                'product_id'            => $value['product_id'],
                'product_quantity_id'   => $productQuantityId,
                'product_color_id'      => $value['color_id'],
                'product_size_id'       => $value['size_id'],
                'brand_id'              => $value['brand_id'],
                'quantity'              => $quantity,
            ]);

            if ($toStoreId > 1) {
                $inventory = StoreInventory::where([
                    'store_id'             => $toStoreId,
                    'product_quantity_id'  => $productQuantityId,
                ])->first();

                if ($inventory) {
                    $inventory->update([
                        'quantity'       => $inventory->quantity + $quantity,
                        'total_quantity' => $inventory->total_quantity + $quantity,
                    ]);
                } else {
                    StoreInventory::create([
                        'store_id'              => $toStoreId,
                        'product_id'            => $value['product_id'],
                        'product_quantity_id'   => $productQuantityId,
                        'product_color_id'      => $value['color_id'],
                        'product_size_id'       => $value['size_id'],
                        'brand_id'              => $value['brand_id'],
                        'quantity'              => $quantity,
                        'total_quantity'        => $quantity
                    ]);
                }
            } else {
                $inventory = StoreInventory::where([
                    'store_id'             => $fromStoreId,
                    'product_quantity_id'  => $productQuantityId,
                ])->first();

                if ($inventory) {
                    $inventory->update([
                        'quantity'       => $inventory->quantity - $quantity,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Items transferred successfully.'
        ]);
    }
}
