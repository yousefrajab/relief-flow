<?php

namespace App\Http\Controllers;

use App\Models\AidRequest;
use App\Models\Shipment;
use App\Models\Warehouse;
use App\Services\AIService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function show(AIService $aiService): View
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'depot_manager']), 403);

        $stats = $this->buildStats();
        $topCategories = collect($stats['top_categories']);
        $weeklyTrend = $this->buildWeeklyTrend();

        $narrative = $aiService->generateImpactReport($stats);

        return view('reports.show', compact('stats', 'narrative', 'topCategories', 'weeklyTrend'));
    }

    public function export(): StreamedResponse
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'depot_manager']), 403);

        $stats = $this->buildStats();
        $weeklyTrend = $this->buildWeeklyTrend();
        $filename = 'impact-report-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($stats, $weeklyTrend) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Deliveries completed', $stats['delivered_count']]);
            fputcsv($handle, ['Active shipments', $stats['active_count']]);
            fputcsv($handle, ['Active warehouses', $stats['warehouse_count']]);
            fputcsv($handle, ['Pending requests', $stats['pending_count']]);
            fputcsv($handle, ['Rejected requests', $stats['rejected_count']]);

            fputcsv($handle, []);
            fputcsv($handle, ['Category', 'Quantity delivered']);
            foreach ($stats['top_categories'] as $category => $quantity) {
                fputcsv($handle, [$category, $quantity]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Week starting', 'Requests submitted', 'Deliveries confirmed']);
            foreach ($weeklyTrend as $week) {
                fputcsv($handle, [$week['label'], $week['requests'], $week['deliveries']]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function buildStats(): array
    {
        $topCategories = Shipment::where('status', 'delivered')
            ->with('aidRequest.requestItems.item')
            ->get()
            ->flatMap(fn ($shipment) => $shipment->aidRequest->requestItems)
            ->groupBy(fn ($requestItem) => $requestItem->item->category)
            ->map(fn ($group) => $group->sum('quantity'))
            ->sortDesc();

        return [
            'delivered_count' => Shipment::where('status', 'delivered')->count(),
            'active_count' => Shipment::where('status', 'dispatched')->count(),
            'warehouse_count' => Warehouse::where('status', 'active')->count(),
            'pending_count' => AidRequest::where('status', 'pending')->count(),
            'rejected_count' => AidRequest::where('status', 'rejected')->count(),
            'top_categories' => $topCategories->toArray(),
        ];
    }

    private function buildWeeklyTrend(int $weeks = 8): array
    {
        $trend = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = now()->subWeeks($i)->endOfWeek();

            $trend[] = [
                'label' => $start->format('M j'),
                'requests' => AidRequest::whereBetween('created_at', [$start, $end])->count(),
                'deliveries' => Shipment::where('status', 'delivered')->whereBetween('delivered_at', [$start, $end])->count(),
            ];
        }

        return $trend;
    }
}
