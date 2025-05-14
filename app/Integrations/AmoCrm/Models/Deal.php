<?php

namespace App\Integrations\AmoCrm\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'external_id',
        'name',
        'price',
        'status_id',
        'responsible_user_id',
        'created_at_amocrm',
        'updated_at_amocrm',
        'pipeline_id',
        'account_id',
    ];
}
