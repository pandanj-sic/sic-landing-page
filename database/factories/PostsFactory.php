<?php

namespace Database\Factories;

use App\Enums\PostType;
use App\Models\Department;
use App\Models\Posts;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Posts>
 */
class PostsFactory extends Factory
{
    protected $model = Posts::class;

    /**
     * @return array{title: string, content: string, department_id: int|null, image_url: string|null, type: PostType, is_published: bool, metadata: array<string, string>|null, start_date: string|null, end_date: string|null, location: string|null}
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(3, true),
            'department_id' => null,
            'image_url' => fake()->optional()->imageUrl(1920, 1080),
            'type' => PostType::Article,
            'is_published' => false,
            'metadata' => null,
            'start_date' => null,
            'end_date' => null,
            'location' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'is_published' => true,
        ]);
    }

    public function event(): static
    {
        return $this->state(fn (): array => [
            'type' => PostType::Event,
            'start_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+2 months')->format('Y-m-d'),
            'location' => fake()->city(),
        ]);
    }

    public function announcement(): static
    {
        return $this->state(fn (): array => [
            'type' => PostType::Announcement,
        ]);
    }

    public function withDepartment(Department $department): static
    {
        return $this->state(fn (): array => [
            'department_id' => $department->id,
        ]);
    }
}
