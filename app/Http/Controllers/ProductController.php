<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $query = Product::with(['brand', 'department', 'productType'])->whereNull('deleted_at');
    
        if ($request->has('search') && !empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('article_code', 'like', "%{$search}%")
                ->orWhere('manufacture_code', 'like', "%{$search}%")
                ->orWhereHas('brand', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('department', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('productType', function($q) use ($search) {
                    $q->where('product_type_name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->product_type_id) {
            $query->where('product_type_id', $request->product_type_id);
        }

        $products = $query->orderBy('id', 'desc') // Changed to 'desc' for descending order
                        ->paginate($request->input('length', 10));

        $data = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $products->total(),
            'recordsFiltered' => $products->total(),
            'data' => $products->items(),
        ];

        return response()->json($data);
    }
}
