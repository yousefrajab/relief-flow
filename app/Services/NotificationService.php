<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    private string $twilioSid;

    private string $twilioToken;

    private string $twilioFrom;

    private string $whatsAppInstance;

    private string $whatsAppToken;

    public function __construct()
    {
        $this->twilioSid = config('services.twilio.sid') ?? '';
        $this->twilioToken = config('services.twilio.token') ?? '';
        $this->twilioFrom = config('services.twilio.from') ?? '';
        $this->whatsAppInstance = config('services.ultramsg.instance_id') ?? '';
        $this->whatsAppToken = config('services.ultramsg.token') ?? '';
    }

    public function sendSMS(string $to, string $message): bool
    {
        if ($this->twilioSid === '' || $this->twilioToken === '' || $this->twilioFrom === '') {
            Log::info("[Simulation SMS] To: {$to} | Message: {$message}");

            return true;
        }

        try {
            $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->twilioSid}/Messages.json", [
                    'From' => $this->twilioFrom,
                    'To' => $to,
                    'Body' => $message,
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('NotificationService::sendSMS failed: '.$e->getMessage());

            return false;
        }
    }

    public function sendWhatsApp(string $to, string $message): bool
    {
        if ($this->whatsAppInstance === '' || $this->whatsAppToken === '') {
            Log::info("[Simulation WhatsApp] To: {$to} | Message: {$message}");

            return true;
        }

        try {
            $response = Http::asForm()
                ->post("https://api.ultramsg.com/{$this->whatsAppInstance}/messages/chat", [
                    'token' => $this->whatsAppToken,
                    'to' => $to,
                    'body' => $message,
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('NotificationService::sendWhatsApp failed: '.$e->getMessage());

            return false;
        }
    }
}
