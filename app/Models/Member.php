<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $role
 * @property int $department_id
 * @property int|null $parent_id
 * @property int $order
 * @property string|null $image_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'department_id',
        'parent_id',
        'order',
        'image_path',
    ];

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<Member, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'parent_id');
    }

    /**
     * @return HasMany<Member, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Member::class, 'parent_id')->orderBy('order');
    }
}
