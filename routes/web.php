<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

// Rute untuk Login & Logout
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute yang membutuhkan user login
Route::middleware(['auth'])->group(function () {
    
    // Rute untuk Shift Kasir (Bisa GET untuk login redirect, POST untuk submit form cup)
    Route::match(['get', 'post'], '/shift/start', [ShiftController::class, 'startShift'])->name('shift.start');
    Route::post('/shift/end', [ShiftController::class, 'endShift'])->name('shift.end');

    // Halaman Kasir (POS)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/order/{order}/struk', [PosController::class, 'struk'])->name('pos.struk');

    // Rute Khusus Admin
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/reports/download', [\App\Http\Controllers\ReportController::class, 'download'])->name('admin.reports.download');
        Route::resource('/admin/products', ProductController::class)->names('products');
        Route::resource('/admin/employees', EmployeeController::class)->names('employees');
    });
});