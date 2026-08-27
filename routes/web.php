<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AidRequestController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    $stats = [
        'delivered' => \App\Models\Shipment::where('status', 'delivered')->count(),
        'warehouses' => \App\Models\Warehouse::where('status', 'active')->count(),
        'coordinators' => \App\Models\User::where('role', 'coordinator')->where('status', 'active')->count(),
    ];

    return view('welcome', compact('stats'));
})->name('welcome');

Route::get('/track/{token}', [ShipmentController::class, 'track'])->name('tracking.show');

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
        Route::get('/help', fn () => view('help'))->name('help');
        Route::get('/map', [MapController::class, 'show'])->name('map.show');
        Route::get('/reports', [ReportController::class, 'show'])->name('reports.show');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');

        Route::get('/items', [ItemController::class, 'index'])->name('items.index');

        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');

        Route::get('/aid-requests', [AidRequestController::class, 'index'])->name('aid-requests.index');
        Route::get('/aid-requests/create', [AidRequestController::class, 'create'])->name('aid-requests.create');
        Route::get('/aid-requests/{aidRequest}', [AidRequestController::class, 'show'])->name('aid-requests.show');
        Route::post('/aid-requests', [AidRequestController::class, 'store'])->name('aid-requests.store');
        Route::post('/aid-requests/{aidRequest}/reject', [AidRequestController::class, 'reject'])->name('aid-requests.reject');
        Route::post('/aid-requests/{aidRequest}/dispatch', [AidRequestController::class, 'dispatch'])->name('aid-requests.dispatch');

        Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
        Route::post('/shipments/{shipment}/deliver', [ShipmentController::class, 'deliver'])->name('shipments.deliver');
        Route::get('/shipments/{shipment}/print', [ShipmentController::class, 'print'])->name('shipments.print');

        Route::middleware('admin')->group(function () {
            Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
            Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
            Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

            Route::post('/items', [ItemController::class, 'store'])->name('items.store');
            Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
            Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

            Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
            Route::post('/users/{user}/approve', [AdminController::class, 'approve'])->name('users.approve');
            Route::post('/users/{user}/reject', [AdminController::class, 'reject'])->name('users.reject');
        });
    });
});
