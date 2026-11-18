<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Plant Nursery Inventory Management System (SET D)
|--------------------------------------------------------------------------
*/

// Redirect root URL to login or plant inventory list
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('plants.index')
        : redirect()->route('login');
});

// 1. LOGIN MANAGEMENT ROUTES (Guest Middleware)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Protected Administrator Routes (Auth Middleware protects against unauthorized access)
Route::middleware('auth')->group(function () {
    // Secure Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 2. PLANT CRUD RESOURCE ROUTES (plants.index, plants.create, plants.store, plants.show, plants.edit, plants.update, plants.destroy)
    Route::resource('plants', PlantController::class);

    // 3. PLANT REPORTS & EXPORT ROUTES
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    });
});
