<?php

namespace App\Integrations\AmoCrm\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\VariableStorageService;
use App\Models\OauthState;
use Illuminate\Support\Str;

class AmoAuthService extends AmoService
{
    public function authorizeWithCode(string $code): void
    {
        $domain = config('services.amocrm.domain');

        $response = Http::post("https://$domain/oauth2/access_token", [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code,
            'redirect_uri'  => $this->redirectUri,
        ]);

        if (!$response->successful()) {
            Log::error('amoCRM auth error', $response->json());
            throw new \Exception('amoCRM token exchange failed.');
        }

        $data = $response->json();

        $this->storage->setEncrypted('amo_access_token', $data['access_token']);
        $this->storage->setEncrypted('amo_refresh_token', $data['refresh_token']);
        $this->storage->setEncrypted('amo_expires_at', now()->addSeconds($data['expires_in'])->toDateTimeString());
    }

    public function generateState(): string
    {
        $state = Str::random(40);

        OauthState::create([
            'state' => $state,
        ]);

        return $state;
    }
}
