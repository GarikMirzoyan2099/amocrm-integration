<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Integrations\AmoCrm\Services\AmoTokenService;
use Illuminate\Support\Facades\Log;

class TokenDaemon extends Command
{
    protected AmoTokenService $amoTokenService;
    protected $signature = 'daemon-token';
    protected $description = 'Раз в минуту проверяем не истек ли токен';

    const SLEEP_TIME = 60 * 30; // Каждые 30 минут


    public function __construct(AmoTokenService $amoTokenService)
    {
        parent::__construct();
        $this->amoTokenService = $amoTokenService;
    }

    public function handle()
    {
        while (true) {
            Log::info('azaza');
            if ($this->amoTokenService->isAccessTokenExpired()) {
                $this->amoTokenService->refreshAccessToken();
            }

            sleep(60);
        }
    }
}
