<?php

namespace App\Integrations\AmoCrm\Controllers;

use Illuminate\Http\Request;
use App\Integrations\AmoCrm\Services\AmoNoteService ;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Integrations\AmoCrm\DTO\DealDto;

class AmoDealController extends Controller
{
    protected AmoNoteService $amoNoteService;

    public function __construct(AmoNoteService  $amoNoteService)
    {
        $this->amoNoteService = $amoNoteService;
    }

    // Обрабатываем хук для "Сделка добавлена"
    public function handleDealCreated(Request $request)
    {
        try {
            $dto = DealDto::fromCreateWebhook($request->all());
            $this->amoNoteService->addNoteToDeal($dto);
            return response()->json(['message' => 'Deal created hook processed']);
        } catch (\Exception $e) {
            Log::error('Error processing deal created hook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to process deal created hook'], 500);
        }
    }

    // Обрабатываем хук для "Сделка изменена"
    public function handleDealUpdated(Request $request)
    {
        try {
            $dto = DealDto::fromUpdateWebhook($request->all());
            $this->amoNoteService->updateNoteOnDeal($dto);
            return response()->json(['message' => 'Deal updated hook processed']);
        } catch (\Exception $e) {
            Log::error('Error processing deal updated hook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to process deal updated hook'], 500);
        }
    }
}
