<?php

namespace Database\Factories;

use App\Models\Carousels;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Carousels>
 */
class CarouselsFactory extends Factory
{
    protected $model = Carousels::class;

    /**
     * @return array{title: string, description: string, button_text: string|null, button_url: string|null, order: int, department_id: int|null, image_url: string}
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'button_text' => fake()->optional()->words(2, true),
            'button_url' => fake()->optional()->url(),
            'order' => fake()->numberBetween(0, 10),
            'department_id' => null,
            'image_url' => fake()->imageUrl(1920, 1080),
        ];
    }

    public function withDepartment(Department $department): static
    {
        return $this->state(fn (): array => [
            'department_id' => $department->id,
        ]);
    }
}
