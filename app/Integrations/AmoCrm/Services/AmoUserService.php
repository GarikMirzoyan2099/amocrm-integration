<?php

namespace App\Integrations\AmoCrm\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AmoUserService extends AmoService
{
    public function getUserById(int $userId): array
    {
        try {
            $accessToken = $this->variableStorageService->getDecrypted('amo_access_token');

            $response = Http::withToken($accessToken)
                ->get("{$this->apiEndpoint}users/{$userId}");

            if ($response->successful()) {
                return $response->json();
            } else {
                // Логируем ошибку, если запрос не удался
                Log::error('Ошибка получения данных о пользователе', [
                    'user_id' => $userId,
                    'response' => $response->body()
                ]);
                return [];
            }
        } catch (\Exception $e) {
            // Логируем исключение в случае ошибки
            Log::error('Ошибка при получении данных о пользователе по ID', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return [];
        }
    }
}