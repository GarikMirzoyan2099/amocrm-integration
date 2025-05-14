<?php

namespace App\Integrations\AmoCrm\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\VariableStorageService;
use App\Integrations\AmoCrm\DTO\DealDto;
use App\Integrations\AmoCrm\DTO\ContactDto;
use App\Integrations\AmoCrm\Models\Deal;
use App\Integrations\AmoCrm\Repositories\DealRepository;

class AmoNoteService extends AmoService
{
    protected AmoUserService $amoUserService;

    public function __construct(VariableStorageService $variableStorageService, AmoUserService $amoUserService, DealRepository $dealRepository)
    {
        parent::__construct($variableStorageService, $dealRepository);
        $this->amoUserService = $amoUserService;
    }

    public function addNoteToDeal(DealDto $dto)
    {
        try {
            $accessToken = $this->variableStorageService->getDecrypted('amo_access_token');
            $user = $this->amoUserService->getUserById($dto->responsible_user_id);

            $responsibleName = $user['name'] ?? 'Неизвестный ответственный';

            $noteText = "Сделка: {$dto->name}\nОтветственный: {$responsibleName} ({$dto->responsible_user_id})\nСоздана: {$dto->created_at_amocrm}";

            $response = Http::withToken($accessToken)
                ->post("{$this->apiEndpoint}leads/{$dto->external_id}/notes", [
                    [
                        'note_type' => 'common',
                        'params' => [
                            'text' => $noteText
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to create note: ' . $response->body());
            }

            $this->dealRepository->create($dto);

            Log::info("Примечание успешно добавлено к сделке #{$dto->external_id}");
        } catch (\Throwable $e) {
            Log::error('Ошибка при создании примечания для сделки', [
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateNoteOnDeal(DealDto $dto)
    {
        try {
            $dealId = $dto->external_id;
            $dealName = $dto->name ?? 'Без названия';
            $responsibleId = $dto->responsible_user_id;
            $updatedAt = $dto->updated_at_amocrm;

            // Получение старой сделки
            $existingDeal = Deal::where('external_id', $dealId)->first();

            if (!$existingDeal) {
                Log::warning("Сделка с ID {$dealId} не найдена для сравнения изменений");
                return;
            }

            $accessToken = $this->variableStorageService->getDecrypted('amo_access_token');

            // Получаем данные ответственного пользователя
            $user = $this->amoUserService->getUserById($responsibleId);
            $responsibleName = $user['name'] ?? 'Неизвестный ответственный';

            // Сравнение изменений
            $fieldsToCompare = [
                'name' => 'Название',
                'price' => 'Цена',
                'status_id' => 'Статус',
                'responsible_user_id' => 'Ответственный',
            ];

            $changedFields = [];

            foreach ($fieldsToCompare as $field => $label) {
                $oldValue = $existingDeal->$field;
                $newValue = $dto->$field;

                if ((string)$oldValue !== (string)$newValue) {
                    $changedFields[] = "{$label}: {$oldValue} → {$newValue}";
                }
            }

            $noteText = "Сделка обновлена: {$dealName}\n";
            $noteText .= "Ответственный: {$responsibleName} ({$responsibleId})\n";
            $noteText .= "Время изменения: {$updatedAt}\n";

            if (!empty($changedFields)) {
                $noteText .= "Изменённые поля:\n" . implode("\n", $changedFields);


                // Отправка запроса на создание примечания (не отправляем, если нет измененных данных)
                $response = Http::withToken($accessToken)
                    ->post("{$this->apiEndpoint}leads/{$dealId}/notes", [
                        [
                            'note_type' => 'common',
                            'params' => [
                                'text' => $noteText
                            ]
                        ]
                    ]);

                if (!$response->successful()) {
                    throw new \Exception('Failed to create note: ' . $response->body());
                }

                $this->dealRepository->update($dto);
            } else {
                $noteText .= "Нет изменений данных.";
            }

            Log::info("Примечание успешно добавлено к сделке #{$dealId} (обновление)");
        } catch (\Throwable $e) {
            Log::error('Ошибка при создании примечания для обновленной сделки', [
                'message' => $e->getMessage()
            ]);
        }
    }

    public function addContactNoteToDeal(ContactDto $dto)
    {
        try {
            $accessToken = $this->variableStorageService->getDecrypted('amo_access_token');

            foreach ($dto->linked_leads_ids as $leadId) {
                $user = $this->amoUserService->getUserById($dto->responsible_user_id);
                $responsibleName = $user['name'] ?? 'Неизвестный';

                $noteText = "Добавлен контакт: {$dto->name}\n";
                $noteText .= "Ответственный: {$responsibleName} ({$dto->responsible_user_id})\n";
                $noteText .= "Создан: {$dto->created_at_amocrm}\n";

                if ($dto->phone) {
                    $noteText .= "Телефон: {$dto->phone}\n";
                }

                $response = Http::withToken($accessToken)
                    ->post("{$this->apiEndpoint}leads/{$leadId}/notes", [
                        [
                            'note_type' => 'common',
                            'params' => [
                                'text' => $noteText
                            ]
                        ]
                    ]);

                if (!$response->successful()) {
                    throw new \Exception("Не удалось создать примечание для сделки {$leadId}: " . $response->body());
                }

                Log::info("Примечание успешно добавлено к сделке #{$leadId} (контакт)");
            }
        } catch (\Throwable $e) {
            Log::error('Ошибка при добавлении примечания к сделке по контакту', [
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateNoteFromContact(ContactDto $dto): void
    {
        try {
            $accessToken = $this->variableStorageService->getDecrypted('amo_access_token');

            $user = $this->amoUserService->getUserById($dto->responsible_user_id);
            $responsibleName = $user['name'] ?? 'Неизвестный';

            $noteText = "Контакт обновлён: {$dto->name}\n";
            $noteText .= "Ответственный: {$responsibleName} ({$dto->responsible_user_id})\n";
            $noteText .= "Время обновления: {$dto->updated_at_amocrm}\n";

            foreach ($dto->linked_leads_ids as $leadId) {
                $response = Http::withToken($accessToken)
                    ->post("{$this->apiEndpoint}leads/{$leadId}/notes", [
                        [
                            'note_type' => 'common',
                            'params' => [
                                'text' => $noteText
                            ]
                        ]
                    ]);

                if (!$response->successful()) {
                    throw new \Exception("Ошибка при добавлении примечания к сделке {$leadId}: " . $response->body());
                }

                Log::info("Примечание об обновлении контакта добавлено к сделке #{$leadId}");
            }

        } catch (\Throwable $e) {
            Log::error('Ошибка при добавлении примечания об изменении контакта', [
                'message' => $e->getMessage()
            ]);
        }
    }
}