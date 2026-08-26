<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AidRequestController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'], true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/account/pending', fn () => view('account-pending'))->name('account.pending');

    Route::middleware('active')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('admin')->group(function () {
            Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
            Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
            Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

            Route::post('/items', [ItemController::class, 'store'])->name('items.store');
            Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
            Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

            Route::post('/users/{user}/approve', [AdminController::class, 'approve'])->name('users.approve');
            Route::post('/users/{user}/reject', [AdminController::class, 'reject'])->name('users.reject');
        });

        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');

        Route::post('/aid-requests', [AidRequestController::class, 'store'])->name('aid-requests.store');
        Route::post('/aid-requests/{aidRequest}/reject', [AidRequestController::class, 'reject'])->name('aid-requests.reject');
        Route::post('/aid-requests/{aidRequest}/dispatch', [AidRequestController::class, 'dispatch'])->name('aid-requests.dispatch');

        Route::post('/shipments/{shipment}/deliver', [ShipmentController::class, 'deliver'])->name('shipments.deliver');
        Route::get('/shipments/{shipment}/print', [ShipmentController::class, 'print'])->name('shipments.print');
    });
});
