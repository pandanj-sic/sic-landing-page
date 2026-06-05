<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\DepartmentMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DepartmentMember>
 */
class DepartmentMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->name(),
            'position' => fake()->jobTitle(),
            'image_url' => null,
        ];
    }
}
