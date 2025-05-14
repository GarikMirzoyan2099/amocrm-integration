<?php

namespace App\Integrations\AmoCrm\DTO;

use Carbon\Carbon;

class DealDto
{
    public int $external_id;
    public string $name;
    public int $price;
    public int $status_id;
    public int $responsible_user_id;
    public Carbon $created_at_amocrm;
    public Carbon $updated_at_amocrm;
    public int $pipeline_id;
    public int $account_id;

    public static function fromCreateWebhook(array $data): self
    {
        $lead = $data['leads']['add'][0];
        $dto = new self();

        $dto->external_id = (int) $lead['id'];
        $dto->name = $lead['name'] ?? 'Без названия';
        $dto->price = $lead['price'];
        $dto->status_id = $lead['status_id'];
        $dto->responsible_user_id = (int) $lead['responsible_user_id'];
        $dto->created_at_amocrm = Carbon::createFromTimestamp($lead['date_create']);
        $dto->updated_at_amocrm = Carbon::createFromTimestamp($lead['updated_at']);
        $dto->pipeline_id = (int) $lead['pipeline_id'];
        $dto->account_id = (int) $lead['account_id'];

        return $dto;
    }

    public static function fromUpdateWebhook(array $data): self
    {
        $lead = $data['leads']['update'][0];
        $dto = new self();

        $dto->external_id = (int) $lead['id'];
        $dto->name = $lead['name'] ?? 'Без названия';
        $dto->price = $lead['price'];
        $dto->status_id = $lead['status_id'];
        $dto->responsible_user_id = (int) $lead['responsible_user_id'];
        $dto->created_at_amocrm = Carbon::createFromTimestamp($lead['date_create']);
        $dto->updated_at_amocrm = Carbon::createFromTimestamp($lead['updated_at']);
        $dto->pipeline_id = (int) $lead['pipeline_id'];
        $dto->account_id = (int) $lead['account_id'];

        return $dto;
    }
}
