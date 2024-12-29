<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;


Route::get('/', [IndexController::class, 'index'])->name('index');

Route::prefix('cart')->name('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add', [CartController::class, 'add'])->name('.add');
    Route::delete('/clear', [CartController::class, 'clearCart'])->name('.clear');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('.remove');
    Route::post('/increase', [CartController::class, 'increaseQuantity'])->name('.increase');
    Route::post('/decrease', [CartController::class, 'decreaseQuantity'])->name('.decrease');
    Route::get('/total', [CartController::class, 'total'])->name('.total');
    Route::get('/item-count', [CartController::class, 'getCartItemCount'])->name('.itemCount');
});

require __DIR__.'/auth.php';
