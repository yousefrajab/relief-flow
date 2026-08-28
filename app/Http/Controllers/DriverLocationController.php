<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverLocationController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        abort_unless(Auth::user()->role === 'driver', 403);

        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        Auth::user()->update([
            'last_latitude' => $request->latitude,
            'last_longitude' => $request->longitude,
            'last_location_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function destroy(): JsonResponse
    {
        abort_unless(Auth::user()->role === 'driver', 403);

        Auth::user()->update([
            'last_latitude' => null,
            'last_longitude' => null,
            'last_location_at' => null,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
