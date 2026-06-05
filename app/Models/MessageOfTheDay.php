<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageOfTheDay extends Model
{
    protected $fillable = [
        'person_name',
        'person_image',
        'message',
        'is_active',
    ];

    /**
     * @return array{is_active: 'boolean'}
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
