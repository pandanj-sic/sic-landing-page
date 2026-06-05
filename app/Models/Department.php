<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'content',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(DepartmentMember::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Posts::class, 'department_id');
    }

    public function carousels(): HasMany
    {
        return $this->hasMany(Carousels::class, 'department_id');
    }
}
