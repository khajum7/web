<?php

use App\Http\Controllers\ProductItemController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::post('/orderPlace', [SaleController::class, 'store']);
Route::post('/sku/details',  [ProductItemController::class, 'productDetails']);

Route::get('/import/items', [ProductItemController::class, 'import']); //one time use
Route::get('/update/sales/items', [ProductItemController::class, 'updateSaleItems']); //one time use
