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
            default => $this->coordinatorDashboard($user),
        };
    }

    private function adminDashboard(): View
    {
        $warehouses = Warehouse::withCount('inventories')->orderBy('id', 'desc')->get();
        $items = Item::orderBy('id', 'desc')->get();
        $pendingUsers = User::where('status', 'pending_verification')->orderBy('created_at')->get();
        $allUsers = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $aidRequests = AidRequest::with(['requestItems.item', 'user'])->orderBy('id', 'desc')->get();
        $lowStockAlerts = Inventory::with(['warehouse', 'item'])->where('quantity', '<', 1000)->get();

        return view('dashboards.admin', [
            'warehouses' => $warehouses,
            'items' => $items,
            'pendingUsers' => $pendingUsers,
            'allUsers' => $allUsers,
            'aidRequests' => $aidRequests,
            'lowStockAlerts' => $lowStockAlerts,
            'totalWarehouses' => $warehouses->count(),
            'totalItems' => $items->count(),
            'pendingRequests' => $aidRequests->where('status', 'pending')->count(),
            'activeShipments' => Shipment::where('status', 'dispatched')->count(),
        ]);
    }

    private function depotManagerDashboard(): View
    {
        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        $items = Item::orderBy('id', 'desc')->get();
        $inventories = Inventory::with(['warehouse', 'item'])->orderBy('id', 'desc')->get();
        $pendingRequests = AidRequest::with(['requestItems.item', 'user'])
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->get();
        $dispatchedShipments = Shipment::with(['aidRequest', 'warehouse'])
            ->where('status', 'dispatched')
            ->orderBy('id', 'desc')
            ->get();
        $lowStockAlerts = Inventory::with(['warehouse', 'item'])->where('quantity', '<', 1000)->get();

        return view('dashboards.depot-manager', compact(
            'warehouses',
            'items',
            'inventories',
            'pendingRequests',
            'dispatchedShipments',
            'lowStockAlerts'
        ));
    }

    private function coordinatorDashboard(User $user): View
    {
        $items = Item::orderBy('name')->get();
        $myRequests = AidRequest::with(['requestItems.item', 'shipment'])
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();
        $myShipmentsAwaitingDelivery = Shipment::with(['aidRequest', 'warehouse'])
            ->whereHas('aidRequest', fn ($query) => $query->where('user_id', $user->id))
            ->where('status', 'dispatched')
            ->orderBy('id', 'desc')
            ->get();

        return view('dashboards.coordinator', compact('items', 'myRequests', 'myShipmentsAwaitingDelivery'));
    }
}
