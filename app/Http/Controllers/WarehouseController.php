<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::withCount('inventories')->orderBy('name')->get();

        return view('warehouses.index', compact('warehouses'));
    }

    public function show(Warehouse $warehouse): View
    {
        $warehouse->load(['inventories.item', 'shipments.aidRequest']);

        return view('warehouses.show', compact('warehouse'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'capacity' => ['required', 'integer', 'min:100'],
        ]);

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'capacity' => $request->capacity,
            'status' => 'active',
        ]);

        return redirect()->route('warehouses.show', $warehouse)->with('success', __('New warehouse has been added successfully.'));
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'capacity' => ['required', 'integer', 'min:100'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $warehouse->update($request->only(['name', 'location', 'latitude', 'longitude', 'capacity', 'status']));

        return redirect()->route('warehouses.show', $warehouse)->with('success', __('Warehouse has been updated successfully.'));
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->shipments()->exists()) {
            return redirect()->route('warehouses.show', $warehouse)->with('error', __('This warehouse has shipment history and cannot be deleted. Mark it inactive instead.'));
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', __('Warehouse has been deleted successfully.'));
    }
}
