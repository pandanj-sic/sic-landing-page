<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DownloadableFolder extends Model
{
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'order',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DownloadableFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DownloadableFolder::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DownloadableFile::class, 'folder_id');
    }
}
