<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;

    protected string $accessToken;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.whatsapp.api_url'), '/');
        $this->accessToken = config('services.whatsapp.access_token');
    }

    public function listGroups(): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'access-token' => $this->accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->get($this->apiUrl.'/core/v2/api/groups/list');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp listGroups Error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function sendImage(
        string $contactId,
        string $base64,
        string $extension,
        string $caption = '',
        int $delayInSeconds = 0
    ): array {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'access-token' => $this->accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl.'/core/v2/api/chats/send-media', [
                    'contactId' => $contactId,
                    'base64' => $base64,
                    'extension' => $extension,
                    'caption' => $caption,
                    'forceSend' => true,
                    'verifyContact' => false,
                    'delayInSeconds' => $delayInSeconds,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error', [
                'message' => $e->getMessage(),
                'contact_id' => $contactId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createGroup(string $name, array $contacts = []): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'access-token' => $this->accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl.'/core/v2/api/groups', [
                    'name' => $name,
                    'contacts' => $contacts,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp createGroup Error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
