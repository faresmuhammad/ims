<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group((function () {

    Route::inertia('/register', 'Register')->name('register');
    Route::inertia('/login', 'Login')->name('login');
    Route::post('/register', [UserController::class, 'store']);
    Route::post('/login', [UserController::class, 'login']);
}));

Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::inertia('/', 'Dashboard');
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);
    Route::resource('products', ProductController::class)->except(['create', 'edit']);
    Route::post('products/upload',[ProductController::class,'import']);

    Route::post('/orders/new/{type?}',[OrderController::class,'newOrder'])->name('order.new');
    Route::get('/orders/{reference_code}/{type?}',[OrderController::class,'show'])->name('order.show');
    Route::get('/orders/product/{code}',[OrderController::class,'getProduct']);
    Route::put('/orders/{order}',[OrderController::class,'update']);
    
    Route::post('/order/items',[OrderController::class,'newItem']);
});
