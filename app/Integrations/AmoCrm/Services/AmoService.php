<?php

namespace App\Integrations\AmoCrm\Services;

use App\Services\VariableStorageService;
use App\Integrations\AmoCrm\Repositories\DealRepository;

class AmoService
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $apiEndpoint;
    protected VariableStorageService $variableStorageService;
    protected DealRepository $dealRepository;

    public function __construct(VariableStorageService $variableStorageService, DealRepository $dealRepository)
    {
        $this->clientId = config('services.amocrm.client_id');
        $this->clientSecret = config('services.amocrm.client_secret');
        $this->redirectUri = config('services.amocrm.redirect_uri');
        $this->apiEndpoint = "https://" . config('services.amocrm.domain') . "/api/v4/";
        $this->variableStorageService = $variableStorageService;
        $this->dealRepository = $dealRepository;
    }
}
