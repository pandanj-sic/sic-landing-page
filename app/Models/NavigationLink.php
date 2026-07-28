<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $label
 * @property string $url
 * @property int $order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NavigationLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'url',
        'order',
        'is_active',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(NavigationLink::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NavigationLink::class, 'parent_id')->orderBy('order');
    }
}
