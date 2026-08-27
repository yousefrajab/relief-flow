<?php

namespace App\Http\Controllers;

use App\Models\AidRequest;
use App\Models\Warehouse;
use Illuminate\Contracts\View\View;

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

        return view('map.show', compact('warehouses', 'aidRequests'));
    }
}
