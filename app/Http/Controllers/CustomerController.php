<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function getCustomers(Request $request)
    {
        $query = Customer::whereNull('deleted_at');

        if ($request->has('search') && !empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name', 'ASC')
            ->paginate($request->input('length', 10));

        $data = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $customers->total(),
            'recordsFiltered' => $customers->total(),
            'data' => $customers->items(),
        ];

        return response()->json($data);
    }
}
