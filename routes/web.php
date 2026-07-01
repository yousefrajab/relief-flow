<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// مسار عرض الصفحة الرئيسية
Route::get('/', [DashboardController::class, 'index']);

// مسار استقبال بيانات المستودع وحفظها
Route::post('/warehouse/store', [DashboardController::class, 'storeWarehouse'])->name('warehouse.store');