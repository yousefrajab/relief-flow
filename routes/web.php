<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

// --- مسارات مصادقة وتسجيل الدخول العامة ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- جميع مسارات لوحة التحكم محمية بمسار تسجيل الدخول ---
Route::middleware(['auth'])->group(function () {
    // عرض لوحة التحكم الرئيسية
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // مسارات إدارة المستودعات (إضافة، تعديل، حذف)
    Route::post('/warehouse/store', [DashboardController::class, 'storeWarehouse'])->name('warehouse.store');
    Route::post('/warehouse/update', [DashboardController::class, 'updateWarehouse'])->name('warehouse.update');
    Route::post('/warehouse/delete/{id}', [DashboardController::class, 'deleteWarehouse'])->name('warehouse.delete');

    // مسارات إدارة المواد الإغاثية (إضافة، تعديل، حذف)
    Route::post('/item/store', [DashboardController::class, 'storeItem'])->name('item.store');
    Route::post('/item/update', [DashboardController::class, 'updateItem'])->name('item.update');
    Route::post('/item/delete/{id}', [DashboardController::class, 'deleteItem'])->name('item.delete');

    // مسار تزويد وتحديث كميات مخزون الشحن
    Route::post('/inventory/store', [DashboardController::class, 'inventoryStore'])->name('inventory.store');

    // مسار استقبال طلب المساعدات الميدانية
    Route::post('/aid-request/store', [DashboardController::class, 'storeAidRequest'])->name('aid_request.store');

    // مسارات الشحنات والموافقات وتأكيد الاستلام
    Route::post('/shipment/dispatch', [DashboardController::class, 'dispatchShipment'])->name('shipment.dispatch');
    Route::post('/shipment/deliver', [DashboardController::class, 'deliverShipment'])->name('shipment.deliver');
    Route::get('/shipment/{id}/print', [DashboardController::class, 'printShipment'])->name('shipment.print');
});