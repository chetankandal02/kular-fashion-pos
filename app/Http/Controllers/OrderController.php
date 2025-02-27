<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\CreditNote;
use App\Models\GiftVoucher;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ProductQuantity;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ReceiptService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function create(Request $request)
    {
        /* DB::beginTransaction();

        try { */
        $salesPersonId = $request->salesPersonId;
        $branchId = User::find($salesPersonId)->branch_id ?? null;
        $totalSaleItems = count($request->input('orderItems', []));
        $totalReturnItems = count($request->input('returnItems', []));

        $orderItems = $request->input('orderItems', []);
        $returnItems = $request->input('returnItems', []);

        if ($branchId) {
            foreach ($returnItems as $returnItem) {
                $productQuantityId = $returnItem['product_quantity_id'];

                if ($branchId > 1) {
                    $storeInventory = StoreInventory::where('store_id', $branchId)->where('product_quantity_id', $returnItem['product_quantity_id'])->first();
                    if($storeInventory){
                        $storeInventory->quantity = $storeInventory->quantity + 1;
                        $storeInventory->save();
                    } else {
                        $productQuantity = ProductQuantity::find($productQuantityId);

                        StoreInventory::create([
                            'store_id'              => $branchId,
                            'product_id'            => $productQuantity->product_id,
                            'product_quantity_id'   => $productQuantityId,
                            'product_color_id'      => $productQuantity->product_color_id,
                            'product_size_id'       => $productQuantity->product_size_id,
                            'brand_id'              => $productQuantity->product->brand_id ?? null,
                            'quantity'              => 1,
                            'total_quantity'        => 0
                        ]);
                    }
                } else {
                    $productQuantity = ProductQuantity::find($productQuantityId);
                    if($productQuantity){
                        $productQuantity->quantity = $productQuantity->quantity + 1;
                        $productQuantity->save();    
                    }
                }
            }
            
            foreach ($orderItems as $orderItem) {
                $productQuantityId = $orderItem['product_quantity_id'];
                if ($branchId > 1) {
                    $storeInventory = StoreInventory::where('store_id', $branchId)->where('product_quantity_id', $orderItem['product_quantity_id'])->first();
                    if($storeInventory){
                        $storeInventory->quantity = $storeInventory->quantity - 1;
                        $storeInventory->save();    
                    } else {
                        $productQuantity = ProductQuantity::find($productQuantityId);

                        StoreInventory::create([
                            'store_id'              => $branchId,
                            'product_id'            => $productQuantity->product_id,
                            'product_quantity_id'   => $productQuantityId,
                            'product_color_id'      => $productQuantity->product_color_id,
                            'product_size_id'       => $productQuantity->product_size_id,
                            'brand_id'              => $productQuantity->product->brand_id ?? null,
                            'quantity'              => -1,
                            'total_quantity'        => 0
                        ]);
                    }
                } else {
                    $productQuantity = ProductQuantity::find($productQuantityId);
                    if($productQuantity){
                        $productQuantity->quantity = $productQuantity->quantity - 1;
                        $productQuantity->save();
                    }
                }
            }
        }

        $totalAmount = collect($orderItems)->sum(function ($item) {
            return $item['price'] * 1;
        }) + collect($returnItems)->sum(function ($item) {
            return $item['price'] * 1;
        });

        $totalPayableAmount = 0;
        $totalOrderPayableAmount = collect($orderItems)->sum(function ($item) {
            $price = isset($item['changedPrice']['amount']) ? $item['changedPrice']['amount'] : $item['price'];
            return $price * 1;
        });

        $totalReturnAmount = collect($returnItems)->sum(function ($item) {
            $price = isset($item['changedPrice']['amount']) ? $item['changedPrice']['amount'] : $item['price'];
            return $price * 1;
        });
        $totalPayableAmount = $totalOrderPayableAmount - $totalReturnAmount;

        $totalPaidAmount = array_sum(array_column($request->paymentInfo, 'amount'));

        $order = Order::create([
            'sales_person_id' => $salesPersonId,
            'branch_id' => $branchId,
            'total_items' => $totalSaleItems + $totalReturnItems,
            'total_return_items' => $totalReturnItems,
            'total_payable_amount' => $totalPayableAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => $totalPaidAmount,
            'source' => 'POS'
        ]);

        foreach ($orderItems as $orderItem) {
            $params = [
                'order_id' => $order->id,
                'flag' => 'SALE',
                'sales_person_id' => $salesPersonId,
                'branch_id' => $branchId
            ];

            $this->createOrderItem($orderItem, $params);
        }

        foreach ($returnItems as $returnItem) {
            $params = [
                'order_id' => $order->id,
                'flag' => 'RETURN',
                'sales_person_id' => $salesPersonId,
                'branch_id' => $branchId
            ];

            $this->createOrderItem($returnItem, $params);
        }

        $paymentMethods = $request->input('paymentInfo', []);
        $exchangeRate = setting("euro_to_pound");

        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod['method'] === 'Euro') {
                $convertedAmount = (float) $paymentMethod['amount'] * $exchangeRate;
            }

            OrderPayment::create([
                'order_id' => $order->id,
                'method' => $paymentMethod['method'],
                'amount' => $paymentMethod['amount'],
                'original_amount' => $convertedAmount ?? $paymentMethod['amount']
            ]);
        }

        try {
            $this->receiptService->printOrderReceipt($order->id);
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully!',
                'order' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print order receipt!',
            ], 200);
        }


        /* } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        } */
    }

    protected function createOrderItem($orderItem, $params)
    {
        $productId = $orderItem['product_id'];
        $product = Product::with('brand', 'supplier')->find($productId);
        if($product){
            $colorDetail = Color::find($orderItem['color_id']);

            OrderItem::create([
                'order_id'  => $params['order_id'],
                'product_id' => $product->id,
                'size_id' => $orderItem['size_id'],
                'size' => $orderItem['size'],
                'color_id' => $orderItem['color_id'],
                'color_name' => $orderItem['color'],
                'color_code' => $colorDetail->code,
                'ui_color_code' => $colorDetail->ui_color_code,
                'brand_id' => $product->brand_id,
                'brand_name' => $product->brand->name,
                'article_code' => $product->article_code,
                'barcode' => $orderItem['barcode'],
                'original_price' => $product->mrp,
                'changed_price' => isset($orderItem['changedPrice']['amount']) ? $orderItem['changedPrice']['amount'] : $product->mrp,
                'changed_price_reason_id' => isset($orderItem['changedPrice']['reasonId']) ? $orderItem['changedPrice']['reasonId'] : null,
                'changed_price_reason' => isset($orderItem['changedPrice']['reason']) ? $orderItem['changedPrice']['reason'] : '',
                'quantity' => 1,
                'description' => $product->short_description,
                'flag' => $params['flag'],
                'sales_person_id' => $params['sales_person_id'],
                'branch_id' => $params['branch_id'],
            ]);
        }
    }

    public function getSales(Request $request)
    {
        $query = Order::with(['salesPerson', 'user', 'orderItems'])->whereNull('deleted_at');
        if ($request->has('article_code') && !empty($request->article_code)) {
            $query->whereHas('orderItems', function ($q) use ($request) {
                $q->where('article_code', 'like', "%" . $request->article_code . "%");
            });
        }

        if ($request->has('sales_start_date') && !empty($request->sales_start_date)) {
            $startDate = \Carbon\Carbon::parse($request->sales_start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if ($request->has('sales_end_date') && !empty($request->sales_end_date)) {
            $endDate = \Carbon\Carbon::parse($request->sales_end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        if ($request->has('search') && !empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('total_items', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('salesPerson', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sales = $query->orderBy('id', 'desc')
            ->paginate($request->input('length', 10));
        $data = $sales->map(function ($sale) {
            $isPriceChanged = false;

            foreach ($sale->orderItems as $orderItem) {
                if (
                    (!is_null($orderItem->changed_price_reason_id) ||
                    !is_null($orderItem->changed_price_reason)) &&
                    $orderItem->original_price != $orderItem->changed_price
                ) {
                    $isPriceChanged = true;
                }
            }

            return [
                'id' => $sale->id,
                'code' => $sale->code,
                'customer_name' => $sale->user ? $sale->user->name : 'N/A',
                'sales_person_name' => $sale->salesPerson ? $sale->salesPerson->name : 'N/A',
                'total_items' => $sale->total_items,
                'total_amount' => $sale->total_amount,
                'is_price_changed' => $isPriceChanged,
                'date_time' => $sale->created_at->format('d-m-y H:i:s'),
            ];
        });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $sales->total(),
            'recordsFiltered' => $sales->total(),
            'data' => $data,
        ]);
    }

    public function getTodaysSales(Request $request)
    {
        $today = Carbon::today();

        $salesData = OrderItem::where('sales_person_id', $request->salesPersonId)->whereDate('created_at', $today)->get();
        $giftVoucherRedeemeds = OrderPayment::whereDate('created_at', $today)
            ->whereHas('order', function ($query) use ($request) {
                $query->where('sales_person_id', $request->salesPersonId);
            })->get();
        $giftVoucherSold = GiftVoucher::where('generated_by', $request->salesPersonId)->whereDate('created_at', $today)->withTrashed()->count();
        $creditNotesIssue = CreditNote::where('generated_by', $request->salesPersonId)->whereDate('created_at', $today)->withTrashed()->count();


        $totals = [
            'saleItems' => $salesData->where('flag', 'SALE')->count(),
            'saleReturns' => $salesData->where('flag', 'RETURN')->count(),
            'miscSales' => number_format($salesData->where('flag', 'SALE')->sum('changed_price'), 2),
            'miscReturns' => number_format($salesData->where('flag', 'RETURN')->sum('changed_price'), 2),
            'giftVouchersSold' => $giftVoucherSold,
            'giftVouchersRedeemed' => number_format($giftVoucherRedeemeds->where('method', 'Gift Voucher')->sum('original_amount'), 2),
            'creditNotesIssued' => $creditNotesIssue,
            'creditNotesRedeemed' => number_format($giftVoucherRedeemeds->where('method', 'Credit Note')->sum('original_amount'), 2)
        ];

        return response()->json([
            'salesData' => $salesData,
            'totals' => $totals,
        ]);
    }

    public function printLastReceipt(){
        try {
            $lastOrder = Order::latest()->first(); 
            if(!$lastOrder){
                return response()->json([
                    'success' => false,
                    'message' => 'No previous sale found!',
                ], 200);
            }
            
            $this->receiptService->printOrderReceipt($lastOrder->id);

            return response()->json([
                'success' => true,
                'message' => 'Receipt printed successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print order receipt!',
            ], 200);
        }
    }

    public function saleDetail($saleId){
        $sale = Order::with('orderItems', 'paymentMethods', 'salesPerson')->find($saleId);
        return response()->json([
            'sale' => $sale,
        ]);
    }
}
