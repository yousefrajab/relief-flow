<?php

namespace App\Http\Controllers;

use App\Models\AidRequest;
use App\Models\Shipment;
use App\Models\Warehouse;
use App\Services\AIService;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function show(AIService $aiService): View
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'depot_manager']), 403);

        $deliveredCount = Shipment::where('status', 'delivered')->count();
        $activeCount = Shipment::where('status', 'dispatched')->count();
        $warehouseCount = Warehouse::where('status', 'active')->count();
        $pendingCount = AidRequest::where('status', 'pending')->count();
        $rejectedCount = AidRequest::where('status', 'rejected')->count();

        $topCategories = Shipment::where('status', 'delivered')
            ->with('aidRequest.requestItems.item')
            ->get()
            ->flatMap(fn ($shipment) => $shipment->aidRequest->requestItems)
            ->groupBy(fn ($requestItem) => $requestItem->item->category)
            ->map(fn ($group) => $group->sum('quantity'))
            ->sortDesc();

        $stats = [
            'delivered_count' => $deliveredCount,
            'active_count' => $activeCount,
            'warehouse_count' => $warehouseCount,
            'pending_count' => $pendingCount,
            'rejected_count' => $rejectedCount,
            'top_categories' => $topCategories->toArray(),
        ];

        $narrative = $aiService->generateImpactReport($stats);

        return view('reports.show', compact('stats', 'narrative', 'topCategories'));
    }
}
