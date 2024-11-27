<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\ProductType;

class FrontendController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->whereNull('deleted_at')->get();
        $productTypes = ProductType::latest()->whereNull('deleted_at')->get();

        return view('index', compact('brands', 'productTypes'));
    }
}
