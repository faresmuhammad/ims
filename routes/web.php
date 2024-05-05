<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;

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
    Route::post('products/upload', [ProductController::class, 'import']);

    //Placing Order
    Route::post('/orders/new/{type?}', [OrderController::class, 'newOrder'])->name('order.new');
    Route::get('/orders/{reference_code}/{type?}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/product/{product:code}', [ProductController::class, 'getProduct']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);

    Route::post('/order/{order:reference_code}/items', [OrderController::class, 'newItem']);
    Route::get('/order/{order:reference_code}/items', [OrderController::class, 'items']);
    Route::put('/order/item/{item}', [OrderController::class, 'updateItem']);
    Route::delete('/order/item/{item}', [OrderController::class, 'deleteItem']);

    //Stocks
    Route::post('/stocks', [StockController::class, 'store']);
    Route::put('/stocks/{stock:code}', [StockController::class, 'update']);

    //Shifts
    Route::get('shifts',[ShiftController::class,'index']);
    Route::post('shift/start', [ShiftController::class, 'start']);
    Route::put('shift/end', [ShiftController::class, 'end']);

});

