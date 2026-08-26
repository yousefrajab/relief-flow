<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'capacity' => ['required', 'integer', 'min:100'],
        ]);

        Warehouse::create([
            'name' => $request->name,
            'location' => $request->location,
            'capacity' => $request->capacity,
            'status' => 'active',
        ]);

        return redirect()->route('dashboard')->with('success', __('New warehouse has been added successfully.'));
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'capacity' => ['required', 'integer', 'min:100'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $warehouse->update($request->only(['name', 'location', 'capacity', 'status']));

        return redirect()->route('dashboard')->with('success', __('Warehouse has been updated successfully.'));
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->shipments()->exists()) {
            return redirect()->route('dashboard')->with('error', __('This warehouse has shipment history and cannot be deleted. Mark it inactive instead.'));
        }

        $warehouse->delete();

        return redirect()->route('dashboard')->with('success', __('Warehouse has been deleted successfully.'));
    }
}
