<?php

namespace App\Integrations\AmoCrm\DTO;

use Carbon\Carbon;

class ContactDto
{
    public function __construct(
        public readonly int $external_id,
        public readonly string $name,
        public readonly int $responsible_user_id,
        public readonly int $account_id,
        public readonly ?string $phone,
        public Carbon $created_at_amocrm,
        public Carbon $updated_at_amocrm,
        public readonly array $linked_leads_ids = [],
    ) {}

    public static function fromCreateWebhook(array $payload): self
    {
        $contact = $payload['contacts']['add'][0];

        // Извлекаем телефон (если есть)
        $phone = null;
        if (!empty($contact['custom_fields'])) {
            foreach ($contact['custom_fields'] as $field) {
                if (($field['code'] ?? '') === 'PHONE') {
                    $phone = $field['values'][0]['value'] ?? null;
                    break;
                }
            }
        }

        // Извлекаем ID сделок
        $linkedLeads = array_keys($contact['linked_leads_id'] ?? []);

        return new self(
            external_id: (int)$contact['id'],
            name: $contact['name'] ?? 'Без имени',
            responsible_user_id: (int)$contact['responsible_user_id'],
            account_id: (int)$contact['account_id'],
            phone: $phone,
            created_at_amocrm: Carbon::createFromTimestamp($contact['date_create']),
            updated_at_amocrm: Carbon::createFromTimestamp($contact['updated_at']),
            linked_leads_ids: $linkedLeads
        );
    }

    public static function fromUpdateWebhook(array $payload): self
    {
        $contact = $payload['contacts']['update'][0];

        // Извлекаем телефон (если есть)
        $phone = null;
        if (!empty($contact['custom_fields'])) {
            foreach ($contact['custom_fields'] as $field) {
                if (($field['code'] ?? '') === 'PHONE') {
                    $phone = $field['values'][0]['value'] ?? null;
                    break;
                }
            }
        }

        // Извлекаем ID сделок
        $linkedLeads = array_keys($contact['linked_leads_id'] ?? []);

        return new self(
            external_id: (int)$contact['id'],
            name: $contact['name'] ?? 'Без имени',
            responsible_user_id: (int)$contact['responsible_user_id'],
            account_id: (int)$contact['account_id'],
            phone: $phone,
            created_at_amocrm: Carbon::createFromTimestamp($contact['date_create']),
            updated_at_amocrm: Carbon::createFromTimestamp($contact['updated_at']),
            linked_leads_ids: $linkedLeads
        );
    }
}
