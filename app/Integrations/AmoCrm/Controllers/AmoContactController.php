<?php

namespace App\Integrations\AmoCrm\Controllers;

use Illuminate\Http\Request;
use App\Integrations\AmoCrm\Services\AmoNoteService ;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Integrations\AmoCrm\DTO\ContactDto;

class AmoContactController extends Controller
{
    protected AmoNoteService $amoNoteService;

    public function __construct(AmoNoteService  $amoNoteService)
    {
        $this->amoNoteService = $amoNoteService;
    }

    // Обрабатываем хук для "Сделка добавлена"
    public function handleContactCreated(Request $request)
    {
        try {
            Log::info('asfd', ['df'=> $request->all()]);
            $dto = ContactDto::fromCreateWebhook($request->all());
            $this->amoNoteService->addContactNoteToDeal($dto);
            return response()->json(['message' => 'Deal created hook processed']);
        } catch (\Exception $e) {
            Log::error('Error processing deal created hook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to process deal created hook'], 500);
        }
    }

    public function handleContactUpdated(Request $request)
    {
        try {
            $dto = ContactDto::fromUpdateWebhook($request->all());
            $this->amoNoteService->updateNoteFromContact($dto);
            return response()->json(['message' => 'Deal updated hook processed']);
        } catch (\Exception $e) {
            Log::error('Error processing deal updated hook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to process deal updated hook'], 500);
        }
    }
}
