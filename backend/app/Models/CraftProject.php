<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CraftProject extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'reference_image_url',
        'tutorial_url',
        'status',
        'progress',
        'start_date',
        'target_date',
        'difficulty',
        'materials',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'start_date' => 'date:Y-m-d',
            'target_date' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}