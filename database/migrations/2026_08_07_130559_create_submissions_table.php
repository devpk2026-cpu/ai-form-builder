<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->json('data_json');

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('submitted_at')->useCurrent();

            $table->timestamps();

            $table->index(['form_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};