<?php

namespace App\Http\Controllers;

use App\Models\AidRequest;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    public function show(): View
    {
        $warehouses = Warehouse::whereNotNull('latitude')->whereNotNull('longitude')->get();
        $aidRequests = AidRequest::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereIn('status', ['pending', 'dispatched'])
            ->with('user')
            ->get();

        $canSeeDrivers = in_array(auth()->user()->role, ['admin', 'depot_manager'], true);

        return view('map.show', compact('warehouses', 'aidRequests', 'canSeeDrivers'));
    }

    public function drivers(): JsonResponse
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'depot_manager'], true), 403);

        $drivers = User::where('role', 'driver')
            ->whereNotNull('last_latitude')
            ->whereNotNull('last_longitude')
            ->where('last_location_at', '>=', now()->subMinutes(10))
            ->get()
            ->map(fn (User $driver) => [
                'id' => $driver->id,
                'name' => $driver->name,
                'latitude' => (float) $driver->last_latitude,
                'longitude' => (float) $driver->last_longitude,
                'updated_at' => $driver->last_location_at->diffForHumans(),
            ])
            ->values();

        return response()->json(['drivers' => $drivers]);
    }
}
