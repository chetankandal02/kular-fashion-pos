<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\ProductType;

class FrontendController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('name', 'ASC')->get();
        $productTypes = ProductType::orderBy('name', 'ASC')->get();

        return view('index', compact('brands', 'productTypes'));
    }

    public function branch()
    {
        $branches = Branch::where('status','Active')->get();

        return response()->json($branches);
    }
}
