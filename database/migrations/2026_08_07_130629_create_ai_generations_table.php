<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('form_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text('prompt');

            $table->string('model')->nullable();

            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();

            $table->unsignedInteger('latency_ms')->nullable();

            $table->string('status', 20)->default('pending');

            $table->json('response_json')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['form_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generations');
    }
};