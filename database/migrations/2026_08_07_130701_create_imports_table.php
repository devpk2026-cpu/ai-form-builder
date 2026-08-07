<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('form_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 20);

            $table->string('status', 20)->default('pending');

            $table->json('parsed_data')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};