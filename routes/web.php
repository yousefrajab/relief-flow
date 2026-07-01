<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// ربط الصفحة الرئيسية بمتحكم لوحة التحكم
Route::get('/', [DashboardController::class, 'index']);