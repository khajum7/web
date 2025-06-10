<?php

use App\Http\Controllers\ProductItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleItemController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/admin', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'inertia.auth']);

Route::get('/admin', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'inertia.auth'])->name('dashboard');

Route::middleware(['auth', 'inertia.auth'])->prefix('admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

Route::middleware(['auth', 'inertia.auth'])->prefix('admin')->group(function () {
    Route::resource('sales', SaleController::class)->except('store');
    Route::get('/sales/{sale}/items/create', [SaleItemController::class, 'create'])->name('sales.items.create');
    Route::get('/sales/{saleItem}/items/edit', [SaleItemController::class, 'edit'])->name('sales.items.edit');
    Route::put('/sales/{saleItem}/items/update', [SaleItemController::class, 'update'])->name('sales.items.update');
    Route::put('/sales/{sale}/note/update', [SaleController::class, 'updateNote'])->name('sales.update.note');
    Route::get('/sales/{sale}/logs', [SaleController::class, 'orderLogs'])->name('sales.order.logs');
    Route::put('/sales/{sale}/status', [SaleController::class, 'updateStatus'])->name('sales.status');
    Route::get('/product/items', [ProductItemController::class, 'index'])->name('product.items.index');
    Route::get('/search', [SaleController::class, 'search'])->name('sales.search');
});
