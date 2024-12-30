<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Request $request){
        $branchId = User::find($request->salesPersonId)->branch_id ?? null;
        $totalSaleItems = count($request->input('orderItems', []));
        $totalReturnItems = count($request->input('returnItems', []));
        $totalItems = $totalSaleItems + $totalReturnItems;

        $order = Order::create([
            'sales_person_id ' => $request->salesPersonId,
            'branch_id' => $branchId,
            'total_items' => $totalItems,
            'source' => 'POS'
        ]);

        print_r($order);
        dd($request->all());
    }
}
