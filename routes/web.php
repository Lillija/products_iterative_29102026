<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTagController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
})->name('home');

Route::post('products/{product}/quantity', [ProductController::class, 'changeQuantity'])
    ->name('products.change-quantity');

Route::get('tags', [TagController::class, 'search'])->name('tags.search');

Route::put('products/{product}/tags', [ProductTagController::class, 'update'])
    ->name('products.tags.update');

Route::resource('products', ProductController::class)
    ->names('products');
