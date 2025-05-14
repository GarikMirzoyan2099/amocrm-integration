<?php

namespace App\Integrations\AmoCrm\Controllers;

use Illuminate\Http\Request;
use App\Integrations\AmoCrm\Services\AmoAuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;


class AmoAuthController extends Controller
{
    protected AmoAuthService $authService;

    public function __construct(AmoAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function redirectToAmoCRM()
    {
        $clientId = config('services.amocrm.client_id');
        $state = $this->authService->generateState();

        $authUrl = "https://www.amocrm.ru/oauth?client_id={$clientId}&state={$state}&mode=popup";

        return Redirect::to($authUrl);
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return response()->json(['error' => 'Missing code parameter'], 400);
        }

        try {
            $this->authService->authorizeWithCode($code);
            return response()->json(['message' => 'Authorization successful']);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Authorization failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
