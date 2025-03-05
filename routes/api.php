<?php

use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GiftVoucherController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LayawayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/layaways', [LayawayController::class, 'store']);

Route::get('/customers', [CustomerController::class, 'getCustomers']);
Route::post('/customers', [CustomerController::class, 'store']);

Route::post('/gift-voucher', [GiftVoucherController::class, 'create']);
Route::post('/print-last-gift-voucher-receipt', [GiftVoucherController::class, 'printLastReceipt']);
Route::post('/gift-voucher/apply', [GiftVoucherController::class, 'apply']);

Route::post('/credit-note', [CreditNoteController::class, 'create']);
Route::post('/credit-note/apply', [CreditNoteController::class, 'apply']);

Route::post('/place-order', [OrderController::class, 'create']);
Route::get('/branches', [FrontendController::class, 'branch']);
Route::post('/products/add-manufacture-barcode', [ProductController::class, 'addManufactureBarcode']);

Route::post('/sales/today', [OrderController::class, 'getTodaysSales']);
Route::post('/print-last-sale-receipt', [OrderController::class, 'printLastReceipt']);

Route::post('/quick-validate-item', [ProductController::class, 'quickValidateItem']);
Route::get('/product-stocks/{product}', [ProductController::class, 'stocksDetail']);
