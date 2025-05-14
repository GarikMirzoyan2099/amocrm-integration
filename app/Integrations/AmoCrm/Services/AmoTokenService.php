<?php

namespace App\Integrations\AmoCrm\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AmoTokenService extends AmoService
{
    public function isAccessTokenExpired(): bool
    {
        $expiresAt = $this->variableStorageService->getDecrypted('amo_expires_at');

        // Вычитаем 31 минуту из времени истечения токена
        $thresholdTime = Carbon::parse($expiresAt)->subSeconds(1830);

        // Если текущее время больше времени с учетом вычитания 31 минуту, то токен считается истекшим (так обезопасим себя от просрочки токена)
        return Carbon::now()->greaterThan($thresholdTime);
    }

        public function refreshAccessToken(): array
        {
            $refreshToken = $this->variableStorageService->getDecrypted('amo_refresh_token');

            if (!$refreshToken) {
                throw new \Exception('Refresh token is missing');
            }

            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post('https://oauth.amocrm.ru/oauth2/access_token', [
                    'grant_type'    => 'refresh_token',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $refreshToken,
                    'redirect_uri'  => $this->redirectUri,
                ]);

                if (!$response->successful()) {
                    throw new \Exception('Failed to refresh access token: ' . $response->body());
                }

                // Получаем новые токены из ответа
                $newAccessToken = $response->json('access_token');
                $newRefreshToken = $response->json('refresh_token');
                $expiresIn = $response->json('expires_in');

                $expiresAt = now()->addSeconds($expiresIn);

                // Сохраняем в переменные с шифрованием
                $this->variableStorageService->setEncrypted('amo_access_token', $newAccessToken);
                $this->variableStorageService->setEncrypted('amo_refresh_token', $newRefreshToken);
                $this->variableStorageService->setEncrypted('amo_token_expires_at', $expiresAt->toDateTimeString());

                Log::info('AmoCRM token refreshed successfully.', ['expires_at' => $expiresAt]);

                return [
                    'access_token' => $newAccessToken,
                    'refresh_token' => $newRefreshToken,
                    'expires_at' => $expiresAt
                ];
            } catch (\Throwable $e) {
                Log::error('Error refreshing access token: ' . $e->getMessage());
                throw $e;
            }
        }

}
