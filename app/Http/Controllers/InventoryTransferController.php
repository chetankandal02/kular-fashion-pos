<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ProductQuantity;
use App\Models\InventoryTransfer;
use App\Models\StoreInventory;
use App\Models\Branch;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\User;
use App\Models\Brand;
use App\Models\InventoryItem;

use Illuminate\Http\Request;

class InventoryTransferController extends Controller
{
    public function InventoryTransferItems(Request $request)
    {
        $fromStoreId = $request->fromStore;
        $toStoreId = $request->toStore;
        $items = $request->items;

        $inventoryTransfer = InventoryTransfer::create([
            'sent_from'     => $fromStoreId,
            'sent_to'       => $toStoreId,
            'sent_by'       => Auth::id()
        ]);

        foreach ($items as $value) {
            $productQuantityId = $value['product_quantity_id'];
            $quantity = $value['quantity'];

            $productQuantity = ProductQuantity::find($productQuantityId);

            InventoryItem::create([
                'inventroy_transfer_id' => $inventoryTransfer->id,
                'product_id'            => $productQuantity->product_id,
                'product_quantity_id'   => $productQuantityId,
                'product_color_id'      => $productQuantity->product_color_id,
                'product_size_id'       => $productQuantity->product_size_id,
                'brand_id'              => $value['brand_id'],
                'quantity'              => $quantity,
            ]);
            
            // If any store expecting default is transfering the item, need to add quantity 0 or get existing quantity
            if($fromStoreId > 1){
                $fromStoreInventory = StoreInventory::firstOrCreate(
                    [
                        'store_id' => $fromStoreId,
                        'product_quantity_id' => $productQuantityId,
                    ],
                    [
                        'product_id' => $productQuantity->product_id,
                        'product_color_id' => $productQuantity->product_color_id,
                        'product_size_id' => $productQuantity->product_size_id,
                        'brand_id' => $value['brand_id'],
                        'quantity' => 0,
                        'total_quantity' => 0,
                    ]
                );
    
                $fromStoreInventory->update([
                    'quantity' => $fromStoreInventory->quantity - $quantity,
                ]);
            }

            // Update stock in default store is tranfered to/from default store
            if ($toStoreId > 1 && $fromStoreId === 1) {
                $productQuantity->update([
                    'quantity' => $productQuantity->quantity - $quantity,
                ]);
            } else if ($toStoreId === 1) {
                $productQuantity->update([
                    'quantity' => $productQuantity->quantity + $quantity,
                    //'total_quantity' => $productQuantity->total_quantity + $quantity,
                ]);
            }

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
                        'product_id'            => $productQuantity->product_id,
                        'product_quantity_id'   => $productQuantityId,
                        'product_color_id'      => $productQuantity->product_color_id,
                        'product_size_id'       => $productQuantity->product_size_id,
                        'brand_id'              => $value['brand_id'],
                        'quantity'              => $quantity,
                        'total_quantity'        => $quantity
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Items transferred successfully.'
        ]);
    }
    public function InventoryTransferHistory(){
        $inventoryTransfer = InventoryTransfer::with('sentFrom', 'sentTo', 'sentBy')->orderBy('inventory_transfers.created_at', 'desc')->get();
        
        return response()->json($inventoryTransfer);
    }
    public function InventoryTransferShow($id){

        $inventoryTransfer = InventoryTransfer::with(
            'sentFrom', 
            'sentTo', 
            'sentBy', 
            'inventoryItems',
            'inventoryItems.product.productType', 
            'inventoryItems.productColor.colorDetail', 
            'inventoryItems.productSize.sizeDetail', 
            'inventoryItems.brand'
        )->findOrFail($id);

        return view('inventory.inventory-transfer-view',compact('inventoryTransfer'));
    }
}
