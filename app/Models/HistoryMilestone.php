<?php

namespace App\Models;

use Database\Factories\HistoryMilestoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMilestone extends Model
{
    /** @use HasFactory<HistoryMilestoneFactory> */
    use HasFactory;

    protected $fillable = [
        'year',
        'title',
        'description',
        'icon',
        'sort_order',
    ];
}
