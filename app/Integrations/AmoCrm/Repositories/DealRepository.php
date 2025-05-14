<?php

namespace App\Integrations\AmoCrm\Repositories;

use App\Integrations\AmoCrm\DTO\DealDto;
use App\Integrations\AmoCrm\Models\Deal;

class DealRepository
{
    public function create(DealDto $dto): Deal
    {
        return Deal::create([
            'external_id' => $dto->external_id,
            'name' => $dto->name,
            'price' => $dto->price,
            'status_id' => $dto->status_id,
            'responsible_user_id' => $dto->responsible_user_id,
            'created_at_amocrm' => $dto->created_at_amocrm,
            'updated_at_amocrm' => $dto->updated_at_amocrm,
            'pipeline_id' => $dto->pipeline_id,
            'account_id' => $dto->account_id,
        ]);
    }

    public function update(DealDto $dto): ?Deal
    {
        $deal = Deal::where('external_id', $dto->external_id)->first();

        if (!$deal) {
            return null;
        }

        $deal->update([
            'name' => $dto->name,
            'price' => $dto->price,
            'status_id' => $dto->status_id,
            'responsible_user_id' => $dto->responsible_user_id,
            'created_at_amocrm' => $dto->created_at_amocrm,
            'updated_at_amocrm' => $dto->updated_at_amocrm,
            'pipeline_id' => $dto->pipeline_id,
            'account_id' => $dto->account_id,
        ]);

        return $deal;
    }
}
