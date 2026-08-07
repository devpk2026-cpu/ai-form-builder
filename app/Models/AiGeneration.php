<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGeneration extends Model
{
    protected $fillable = [
        'user_id',
        'form_id',
        'prompt',
        'model',
        'input_tokens',
        'output_tokens',
        'latency_ms',
        'status',
        'response_json',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'response_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}