<?php

namespace App\Services;

use App\Models\AidRequest;
use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Support\Collection;

class LogisticsService
{
    public function distanceKm(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): ?float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return null;
        }

        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }

    public function rankWarehousesFor(AidRequest $aidRequest): Collection
    {
        $aidRequest->loadMissing('requestItems');
        $warehouses = Warehouse::where('status', 'active')->get();

        return $warehouses->map(function (Warehouse $warehouse) use ($aidRequest) {
            $canFulfill = true;
            $shortfalls = [];

            foreach ($aidRequest->requestItems as $requestItem) {
                $available = Inventory::where('warehouse_id', $warehouse->id)
                    ->where('item_id', $requestItem->item_id)
                    ->value('quantity') ?? 0;

                if ($available < $requestItem->quantity) {
                    $canFulfill = false;
                    $shortfalls[] = $requestItem->item->name;
                }
            }

            $distance = $this->distanceKm(
                $warehouse->latitude,
                $warehouse->longitude,
                $aidRequest->latitude,
                $aidRequest->longitude
            );

            return [
                'warehouse' => $warehouse,
                'distance_km' => $distance,
                'can_fulfill' => $canFulfill,
                'shortfalls' => $shortfalls,
            ];
        })->sortBy([
            fn ($a, $b) => $b['can_fulfill'] <=> $a['can_fulfill'],
            fn ($a, $b) => ($a['distance_km'] ?? PHP_FLOAT_MAX) <=> ($b['distance_km'] ?? PHP_FLOAT_MAX),
        ])->values();
    }
}
