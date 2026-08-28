<?php

namespace App\Services;

use App\Models\AidRequest;
use App\Models\Inventory;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? '';
    }

    private function enabled(): bool
    {
        return $this->apiKey !== '';
    }

    public function verifyDeliveryPhoto(UploadedFile $photo, array $expectedItems): array
    {
        if (! $this->enabled()) {
            return [
                'status' => 'needs_review',
                'notes' => 'AI verification is not configured (simulation mode). A human should review this delivery photo manually.',
            ];
        }

        try {
            $base64 = base64_encode(file_get_contents($photo->getRealPath()));
            $itemList = implode(', ', $expectedItems);

            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You verify humanitarian aid delivery photos. Respond ONLY with JSON: {"status": "verified"|"needs_review", "notes": "short explanation"}. Use "verified" only if the photo plausibly shows relief goods matching the expected items being received or unloaded. Otherwise use "needs_review".',
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => "Expected items in this delivery: {$itemList}"],
                                ['type' => 'image_url', 'image_url' => ['url' => "data:image/jpeg;base64,{$base64}"]],
                            ],
                        ],
                    ],
                ]);

            $content = $response->json('choices.0.message.content');
            $decoded = json_decode((string) $content, true);

            if (isset($decoded['status'])) {
                return $decoded;
            }
        } catch (\Throwable $e) {
            Log::warning('AIService::verifyDeliveryPhoto failed: '.$e->getMessage());
        }

        return [
            'status' => 'needs_review',
            'notes' => 'Automatic verification could not be completed. Please review manually.',
        ];
    }

    public function classifyPriority(string $location, ?string $notes): string
    {
        if (! $this->enabled()) {
            $text = strtolower($notes ?? '');
            foreach (['urgent', 'emergency', 'critical', 'children', 'injured', 'medical'] as $keyword) {
                if (str_contains($text, $keyword)) {
                    return 'critical';
                }
            }

            return 'normal';
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You triage humanitarian aid requests. Respond ONLY with JSON: {"priority": "critical"|"high"|"normal"}.',
                        ],
                        [
                            'role' => 'user',
                            'content' => "Location: {$location}\nNotes: ".($notes ?? 'none'),
                        ],
                    ],
                ]);

            $decoded = json_decode((string) $response->json('choices.0.message.content'), true);

            if (isset($decoded['priority']) && in_array($decoded['priority'], ['critical', 'high', 'normal'], true)) {
                return $decoded['priority'];
            }
        } catch (\Throwable $e) {
            Log::warning('AIService::classifyPriority failed: '.$e->getMessage());
        }

        return 'normal';
    }

    public function generateImpactReport(array $stats): string
    {
        if (! $this->enabled()) {
            return sprintf(
                "Between the platform's founding and today, ReliefFlow coordinated %d completed deliveries across %d active warehouses, distributing relief items to families in need. %d aid requests are currently pending review, reflecting continued demand in the field. This automated summary was generated in simulation mode; connect an OpenAI API key for a fully narrative AI-written report.",
                $stats['delivered_count'] ?? 0,
                $stats['warehouse_count'] ?? 0,
                $stats['pending_count'] ?? 0,
            );
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You write short, factual humanitarian impact summaries (3-4 sentences) for a relief logistics platform, based only on the statistics given. No embellishment, no invented numbers.',
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode($stats),
                        ],
                    ],
                ]);

            $text = $response->json('choices.0.message.content');

            if (! empty($text)) {
                return $text;
            }
        } catch (\Throwable $e) {
            Log::warning('AIService::generateImpactReport failed: '.$e->getMessage());
        }

        return 'Impact report could not be generated at this time.';
    }

    public function answerAssistantQuestion(string $question, User $user): string
    {
        // A tracking token is looked up directly and permission-checked here, in both
        // real and simulated modes — the LLM snapshot never includes other users' shipment
        // details, so it could never answer this on its own even when enabled.
        if (preg_match('/RF-[A-Z0-9]{6,}/i', $question, $matches)) {
            return $this->lookupShipmentByToken($matches[0], $user);
        }

        if (! $this->enabled()) {
            return $this->simulateAssistantAnswer($question, $user);
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are the ReliefFlow assistant, helping a {$user->role} named {$user->name} with humanitarian logistics questions. Answer ONLY using the JSON snapshot of live platform data provided below — never invent numbers or shipment details, and never reveal data that is not in the snapshot. If the answer isn't in the snapshot, say you don't have that information. Keep answers to 1-3 short sentences, in the same language the user asked in.\n\nLive data snapshot:\n".json_encode($this->assistantSnapshot($user)),
                        ],
                        [
                            'role' => 'user',
                            'content' => $question,
                        ],
                    ],
                ]);

            $text = $response->json('choices.0.message.content');

            if (! empty($text)) {
                return $text;
            }
        } catch (\Throwable $e) {
            Log::warning('AIService::answerAssistantQuestion failed: '.$e->getMessage());
        }

        return __('The assistant could not answer that right now. Please try again.');
    }

    private function assistantSnapshot(User $user): array
    {
        return match ($user->role) {
            'admin', 'depot_manager' => [
                'role' => $user->role,
                'pending_aid_requests' => AidRequest::where('status', 'pending')->count(),
                'active_shipments' => Shipment::whereIn('status', ['dispatched', 'picked_up'])->count(),
                'delivered_shipments' => Shipment::where('status', 'delivered')->count(),
                'active_warehouses' => Warehouse::where('status', 'active')->count(),
                'low_stock_items' => Inventory::with(['warehouse', 'item'])->where('quantity', '<', 1000)->get()
                    ->map(fn ($i) => "{$i->item->name} @ {$i->warehouse->name}: {$i->quantity}")->values(),
                'pending_account_approvals' => User::where('status', 'pending_verification')->count(),
            ],
            'driver' => [
                'role' => 'driver',
                'active_deliveries' => Shipment::where('driver_user_id', $user->id)->whereIn('status', ['dispatched', 'picked_up'])->count(),
                'delivered_count' => Shipment::where('driver_user_id', $user->id)->where('status', 'delivered')->count(),
                'active_delivery_destinations' => Shipment::with('aidRequest')->where('driver_user_id', $user->id)->whereIn('status', ['dispatched', 'picked_up'])->get()
                    ->map(fn ($s) => $s->aidRequest->location)->values(),
            ],
            default => [
                'role' => 'coordinator',
                'my_pending_requests' => AidRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
                'my_dispatched_requests' => AidRequest::where('user_id', $user->id)->where('status', 'dispatched')->count(),
                'my_delivered_requests' => AidRequest::where('user_id', $user->id)->where('status', 'delivered')->count(),
            ],
        };
    }

    private function simulateAssistantAnswer(string $question, User $user): string
    {
        $normalized = mb_strtolower(trim($question));

        $intents = match ($user->role) {
            'admin', 'depot_manager' => [
                [['طلب', 'معلق', 'pending request'], fn () => __(':count aid request(s) are currently pending review.', ['count' => AidRequest::where('status', 'pending')->count()])],
                [['شحن', 'قيد التوصيل', 'ترحيل', 'active shipment', 'dispatched'], fn () => __(':count shipment(s) are currently dispatched and in transit.', ['count' => Shipment::whereIn('status', ['dispatched', 'picked_up'])->count()])],
                [['مخزون', 'منخفض', 'low stock'], fn () => $this->simulateLowStockAnswer()],
                [['مستودع', 'warehouse'], fn () => __(':count active warehouse(s) in the system.', ['count' => Warehouse::where('status', 'active')->count()])],
                [['حساب', 'موافقة', 'pending account'], fn () => __(':count account(s) are awaiting approval.', ['count' => User::where('status', 'pending_verification')->count()])],
            ],
            'driver' => [
                [['توصيل', 'شحن', 'deliver'], fn () => $this->simulateDriverDeliveriesAnswer($user)],
            ],
            default => [
                [['طلب', 'request'], fn () => $this->simulateCoordinatorRequestsAnswer($user)],
            ],
        };

        foreach ($intents as [$keywords, $handler]) {
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, mb_strtolower($keyword))) {
                    return $handler();
                }
            }
        }

        return $this->simulateFallbackAnswer($user);
    }

    private function lookupShipmentByToken(string $token, User $user): string
    {
        $shipment = Shipment::with('aidRequest')->where('qr_code_token', strtoupper($token))->first();

        if (! $shipment || $user->cannot('view', $shipment)) {
            return __('No shipment found with that tracking token, or you do not have access to it.');
        }

        $statusLabel = match ($shipment->status) {
            'picked_up' => __('In Transit'),
            'delivered' => __('Delivered'),
            default => __('Dispatched'),
        };

        return __('Shipment :token is currently ":status". Destination: :location.', [
            'token' => $shipment->qr_code_token,
            'status' => $statusLabel,
            'location' => $shipment->aidRequest->location,
        ]);
    }

    private function simulateLowStockAnswer(): string
    {
        $items = Inventory::with(['warehouse', 'item'])->where('quantity', '<', 1000)->limit(5)->get();

        if ($items->isEmpty()) {
            return __('No items are currently below the low-stock threshold.');
        }

        $list = $items->map(fn ($i) => "{$i->item->name} ({$i->warehouse->name}): ".number_format($i->quantity))->implode(', ');

        return __('Low stock alert for: :items', ['items' => $list]);
    }

    private function simulateDriverDeliveriesAnswer(User $user): string
    {
        return __('You have :active active delivery task(s) and have completed :delivered so far.', [
            'active' => Shipment::where('driver_user_id', $user->id)->whereIn('status', ['dispatched', 'picked_up'])->count(),
            'delivered' => Shipment::where('driver_user_id', $user->id)->where('status', 'delivered')->count(),
        ]);
    }

    private function simulateCoordinatorRequestsAnswer(User $user): string
    {
        return __('You have :pending pending, :dispatched dispatched, and :delivered delivered request(s).', [
            'pending' => AidRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'dispatched' => AidRequest::where('user_id', $user->id)->where('status', 'dispatched')->count(),
            'delivered' => AidRequest::where('user_id', $user->id)->where('status', 'delivered')->count(),
        ]);
    }

    private function simulateFallbackAnswer(User $user): string
    {
        $examples = match ($user->role) {
            'admin', 'depot_manager' => __('pending requests, active shipments, low stock, or a tracking number like RF-XXXXXXXX'),
            'driver' => __('your active deliveries, or a tracking number like RF-XXXXXXXX'),
            default => __('your requests, or a tracking number like RF-XXXXXXXX'),
        };

        return __('I can help with quick questions about the platform — try asking about :examples.', ['examples' => $examples]);
    }
}
