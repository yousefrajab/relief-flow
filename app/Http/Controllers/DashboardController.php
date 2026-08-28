<?php

namespace App\Http\Controllers;

use App\Models\AidRequest;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'depot_manager' => $this->depotManagerDashboard(),
            'driver' => $this->driverDashboard($user),
            default => $this->coordinatorDashboard($user),
        };
    }

    private function adminDashboard(): View
    {
        return view('dashboards.admin', [
            'totalWarehouses' => Warehouse::count(),
            'totalItems' => Item::count(),
            'pendingRequests' => AidRequest::where('status', 'pending')->count(),
            'activeShipments' => Shipment::where('status', 'dispatched')->count(),
            'pendingUsersCount' => User::where('status', 'pending_verification')->count(),
            'lowStockAlerts' => Inventory::with(['warehouse', 'item'])->where('quantity', '<', 1000)->limit(6)->get(),
            'recentRequests' => AidRequest::with(['requestItems.item', 'user'])->orderBy('id', 'desc')->limit(5)->get(),
        ]);
    }

    private function depotManagerDashboard(): View
    {
        return view('dashboards.depot-manager', [
            'pendingRequestsCount' => AidRequest::where('status', 'pending')->count(),
            'dispatchedCount' => Shipment::where('status', 'dispatched')->count(),
            'lowStockAlerts' => Inventory::with(['warehouse', 'item'])->where('quantity', '<', 1000)->limit(6)->get(),
            'pendingRequests' => AidRequest::with(['requestItems.item', 'user'])
                ->where('status', 'pending')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }

    private function driverDashboard(User $user): View
    {
        return view('dashboards.driver', [
            'activeDeliveries' => Shipment::with(['aidRequest', 'warehouse'])
                ->where('driver_user_id', $user->id)
                ->where('status', 'dispatched')
                ->orderBy('id', 'desc')
                ->get(),
            'deliveredCount' => Shipment::where('driver_user_id', $user->id)->where('status', 'delivered')->count(),
            'recentDeliveries' => Shipment::with(['aidRequest', 'warehouse'])
                ->where('driver_user_id', $user->id)
                ->where('status', 'delivered')
                ->orderBy('delivered_at', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }

    private function coordinatorDashboard(User $user): View
    {
        return view('dashboards.coordinator', [
            'myRequestsCount' => AidRequest::where('user_id', $user->id)->count(),
            'myPendingCount' => AidRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'myShipmentsAwaitingDelivery' => Shipment::with(['aidRequest', 'warehouse'])
                ->whereHas('aidRequest', fn ($query) => $query->where('user_id', $user->id))
                ->where('status', 'dispatched')
                ->orderBy('id', 'desc')
                ->get(),
            'recentRequests' => AidRequest::with(['requestItems.item', 'shipment'])
                ->where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }
}
