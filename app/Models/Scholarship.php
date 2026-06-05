<?php

namespace App\Models;

use Database\Factories\ScholarshipFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    /** @use HasFactory<ScholarshipFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'coverage',
        'description',
        'requirements',
        'is_active',
        'sort_order',
    ];
}
