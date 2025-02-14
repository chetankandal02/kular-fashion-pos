<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductQuantity;
use App\Models\Size;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $query = Product::with(['brand', 'colors.colorDetail', 'sizes.sizeDetail', 'department', 'productType'])->whereNull('deleted_at');

        if ($request->has('search') && !empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('article_code', 'like', "%{$search}%")
                    ->orWhere('manufacture_code', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('productType', function ($q) use ($search) {
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

    public function generateCheckDigit($ean)
    {

        if (strlen($ean) != 12) {
            throw new \Exception("EAN must have 12 digits.");
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $ean[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $remainder = $sum % 10;
        return $remainder === 0 ? 0 : 10 - $remainder;
    }

    public function productValidate($barcode)
    {
        $branch_id = Auth::user()->branch_id;
        $products = ProductQuantity::with('product.brand', 'product.department', 'sizes.sizeDetail', 'colors.colorDetail')->get();
        foreach ($products as $product) {
            $article_code = $product->product->article_code;
            $color_code = $product->colors->colorDetail->color_code;
            $size_code = $product->sizes->sizeDetail->new_code;
            $article_code = $article_code . $color_code . $size_code;
            $checkCode = $this->generateCheckDigit($article_code);
            $generated_code = $article_code . $checkCode;

            if ($generated_code == $barcode) {
                $available_quantity = 0;
                if ($branch_id === 1) {
                    $available_quantity = $product->quantity;
                } else {
                    $inventory = StoreInventory::where([
                        'store_id'             => $branch_id,
                        'product_quantity_id'  => $product->id,
                    ])->first();

                    $available_quantity = $inventory ? $inventory->quantity : 0;
                }

                $productData = [
                    'id' => $product->id,
                    'product_id'   => $product->product->id,
                    'code' => $product->product->article_code,
                    'description' => $product->product->short_description,
                    'product_quantity_id' => $product->id,
                    'color' => $product->colors->colorDetail->color_name,
                    'color_id' => $product->colors->colorDetail->id,
                    'size' => $product->sizes->sizeDetail->size,
                    'size_id' => $product->sizes->sizeDetail->id,
                    'brand' => $product->product->brand->name,
                    'brand_id' => $product->product->brand->id,
                    'price' => (float) $product->sizes->mrp,
                    'available_quantity' => $available_quantity,
                    'manufacture_barcode' => $product->manufacture_barcode,
                    'barcode' => $barcode,
                ];

                return response()->json(['success' => true, 'message' => 'Product barcode is valid.', 'product' => $productData], 200);
            }
        }
        return response()->json(['success' => false, 'message' => 'Product barcode is invalid.']);
    }

    public function quickValidateItem(Request $request)
    {
        $branch_id = User::find($request->userId)->branch_id;

        $product = ProductQuantity::where('product_id', $request->id)->where('product_size_id', $request->sizeId)->where('product_color_id', $request->colorId)->with('product.brand', 'product.department', 'sizes.sizeDetail', 'colors.colorDetail')->first();
        
        if(!$product){
            return response()->json(['success' => false, 'message' => 'Could not found product']);
        }

        $available_quantity = 0;
        if ($branch_id === 1) {
            $available_quantity = $product->quantity;
        } else {
            $inventory = StoreInventory::where([
                'store_id'             => $branch_id,
                'product_quantity_id'  => $product->id,
            ])->first();

            $available_quantity = $inventory ? $inventory->quantity : 0;
        }

        $article_code = $product->product->article_code;
        $color_code = $product->colors->colorDetail->color_code;
        $size_code = $product->sizes->sizeDetail->new_code;
        $article_code = $article_code . $color_code . $size_code;
        $checkCode = $this->generateCheckDigit($article_code);
        $generated_code = $article_code . $checkCode;
        
        $productData = [
            'id' => $product->id,
            'product_id'   => $product->product->id,
            'code' => $product->product->article_code,
            'description' => $product->product->short_description,
            'product_quantity_id' => $product->id,
            'color' => $product->colors->colorDetail->color_name,
            'color_id' => $product->colors->colorDetail->id,
            'size' => $product->sizes->sizeDetail->size,
            'size_id' => $product->sizes->sizeDetail->id,
            'brand' => $product->product->brand->name,
            'brand_id' => $product->product->brand->id,
            'price' => (float) $product->sizes->mrp,
            'available_quantity' => $available_quantity,
            'manufacture_barcode' => $product->manufacture_barcode,
            'barcode' => $generated_code,
        ];

        return response()->json(['success' => true, 'message' => 'Product found successfullly!', 'product' => $productData], 200);
    }

    public function stocksDetail(Product $product){
        $product = Product::with(['sizes.sizeDetail', 'colors.colorDetail', 'quantities'])->find($product->id);

        $branches = Branch::with(['inventory' => function($query) use ($product) {
            $query->where('product_id', $product->id);
        }])->get();

        return response()->json(['success' => true, 'product' => $product, 'branches' => $branches], 200);
    }

    public function addManufactureBarcode(Request $request)
    {
        $targetedItem = ProductQuantity::find($request->id);
        if (!$targetedItem) {
            return response()->json([
                'success' => false
            ]);
        }

        $targetedItem->manufacture_barcode = $request->barcode;
        $targetedItem->save();

        return response()->json([
            'success' => true
        ]);
    }
}
