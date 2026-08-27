<?php

namespace App\Services;

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
}
