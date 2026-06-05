<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadableFile extends Model
{
    protected $fillable = [
        'folder_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DownloadableFolder::class, 'folder_id');
    }
}
