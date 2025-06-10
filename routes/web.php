<?php

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::middleware(['inertia'])->get('/', [PublicController::class, 'index'])->name('public.index');
Route::middleware(['inertia'])->get('/shipping-address', [PublicController::class, 'shippingAddress'])->name('public.shipping-address');
Route::middleware(['inertia'])->get('/jersey-set', [PublicController::class, 'jerseySet'])->name('public.jersey-set');
Route::middleware(['inertia'])->get('/order-preview', [PublicController::class, 'orderPreview'])->name('public.order-preview');
Route::middleware(['inertia'])->get('/thank-you', [PublicController::class, 'thankYou'])->name('public.thank-you');

require __DIR__.'/auth.php';

require __DIR__.'/admin.php';

