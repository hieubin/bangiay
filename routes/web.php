<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;

// 🌐 Trang chủ - Adidas Style
Route::get('/', [HomeController::class, 'index'])->name('home');

// 🔑 Auth routes (login, register, forgot password...)
Auth::routes();

// 🏠 Trang home sau khi đăng nhập (redirect về trang chủ)
Route::get('/home', function () {
    return redirect('/');
});

// 🛒 Giỏ hàng (user phải đăng nhập mới dùng được)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/remove/{rowId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // 📦 Đơn hàng (Checkout & lịch sử đơn)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// 👨‍💼 Admin routes (chỉ admin mới vào được)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('products', ProductController::class);
});
