<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
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

        return redirect()->route('dashboard')->with('success', __('Inventory stock has been updated successfully.'));
    }
}
