<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('external_id')->unique(); // ID из AmoCRM
            $table->string('name')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('responsible_user_id')->nullable();
            $table->timestamp('created_at_amocrm')->nullable();
            $table->timestamp('updated_at_amocrm')->nullable();
            $table->unsignedBigInteger('pipeline_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};