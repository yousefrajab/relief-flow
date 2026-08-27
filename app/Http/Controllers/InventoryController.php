<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(): View
    {
        $inventories = Inventory::with(['warehouse', 'item'])->orderBy('quantity')->get();
        $warehouses = Warehouse::where('status', 'active')->orderBy('name')->get();
        $items = Item::orderBy('name')->get();

        return view('inventory.index', compact('inventories', 'warehouses', 'items'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'depot_manager']), 403);

        $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $inventory = Inventory::firstOrNew([
            'warehouse_id' => $request->warehouse_id,
            'item_id' => $request->item_id,
        ]);

        $inventory->quantity = ($inventory->quantity ?? 0) + $request->quantity;
        $inventory->save();

        return redirect()->route('inventory.index')->with('success', __('Inventory stock has been updated successfully.'));
    }
}
