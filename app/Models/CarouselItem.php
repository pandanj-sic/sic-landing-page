<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $title
 * @property string|null $subtitle
 * @property string $image_path
 * @property string|null $link_url
 * @property int $order
 * @property int|null $department_id
 * @property bool $show_on_main
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CarouselItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'link_url',
        'order',
        'department_id',
        'show_on_main',
    ];

    protected $casts = [
        'show_on_main' => 'boolean',
    ];

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
