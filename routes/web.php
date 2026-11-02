<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
})->name('home');

Route::post('products/{product}/quantity', [ProductController::class, 'changeQuantity'])
    ->name('products.change-quantity');

Route::resource('products', ProductController::class)
    ->names('products');
