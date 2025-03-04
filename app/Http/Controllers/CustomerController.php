<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:50',
            'customer_email' => 'required|email|max:100|unique:customers,email',
            'contact_number' => 'required|string|max:15|unique:customers,phone_number',
            'company_name' => 'nullable|string|max:75',
            'address' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer =  Customer::create([
            'name' => $request->customer_name,
            'email' => $request->customer_email,
            'phone_number' => $request->contact_number,
            'company_name' => $request->company_name,
            'address' => $request->address
        ]);

        return response()->json([
            'success' => true,
            'message' => 'New Customer created successfully!',
            'customer' => $customer
        ], 201);
    }
}
