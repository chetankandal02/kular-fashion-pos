<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Layaway;
use App\Models\LayawayPayment;
use App\Models\Order;
use App\Services\ReceiptService;

class LayawayController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }
    public function store(Request $request)
    {
        $request->validate([
            'orderId' => 'required',
            'customerId' => 'required',
        ]);

        $order = Order::find($request->orderId);
        if(!$order){
            return response()->json([
                'success' => false,
                'message' => 'Order not found!',
            ], 200);  
        }

        $pendingBalance = $order->total_payable_amount - $order->paid_amount;

        $customer = Customer::find($request->customerId);
        $customer->balance += $pendingBalance;
        $customer->save();

        if(!$customer){
            return response()->json([
                'success' => false,
                'message' => 'Customer not found!',
            ], 200);  
        }

        $layaway = Layaway::create([
            'customer_id'       => $request->customerId,
            'order_id'          => $request->orderId,
            'total_amount'      => $order->total_payable_amount,
            'paid_amount'       => $order->paid_amount,
            'balance'           => $pendingBalance,
            'sales_person_id'   => $order->sales_person_id,
            'note'              => $request->note,
        ]); 

        $paymentMethods = $request->input('paymentMethods', []);
        $exchangeRate = setting("euro_to_pound");

        foreach($paymentMethods as $paymentMethod){
            if ($paymentMethod['method'] === 'Euro') {
                $convertedAmount = (float) $paymentMethod['amount'] * $exchangeRate;
            }

            LayawayPayment::create([
                'layaway_id' => $layaway->id,
                'customer_id' => $request->customerId,
                'method' => $paymentMethod['method'],
                'amount' => $paymentMethod['amount'],
                'original_amount' => $convertedAmount ?? $paymentMethod['amount'],
                'sales_person_id' => $order->sales_person_id,
                'payment_date' => now(),
            ]);    
        }

        try {
            $this->receiptService->printOrderReceipt($request->orderId);
            return response()->json([
                'success' => true,
                'message' => 'Layaway processed successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print order receipt!',
            ], 200);
        }
    }

    public function getLayaways(Request $request)
    {
        $query = Layaway::with(['customer', 'order', 'order.orderItems', 'payments']);

        if ($request->has('search') && !empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('order', function ($q) use ($search) {
                        $q->where('code', 'like', "%{$search}%");
                    });
            });
        }

        $layaways = $query->orderBy('id', 'desc')
            ->paginate($request->input('length', 10));

        $data = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $layaways->total(),
            'recordsFiltered' => $layaways->total(),
            'data' => $layaways->items(),
        ];

        return response()->json($data);
    }

    public function update(Request $request, Layaway $layaway){
        $paidAmount = $request->amount;
        if ($request->paymentMethod === 'Euro') {
            $exchangeRate = setting("euro_to_pound");
            $paidAmount = (float) $request->amount * $exchangeRate;
        }

        $layaway->paid_amount += $paidAmount;
        $layaway->balance -= $paidAmount;
        $layaway->note = $request->note;
        $layaway->save();

        $customer = Customer::find($layaway->customer_id);
        if($customer){
            $customer->balance -= $paidAmount;
            $customer->save();    
        }

        LayawayPayment::create([
            'layaway_id' => $layaway->id,
            'customer_id' => $layaway->customer_id,
            'method' => $request->paymentMethod,
            'amount' => $paidAmount,
            'original_amount' => $request->amount,
            'sales_person_id' => $request->salesPersonId,
            'payment_date' => now(),
        ]);

        try {
            $this->receiptService->printOrderReceipt($layaway->order_id);
            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print order receipt!',
            ], 200);
        }

    }
}
