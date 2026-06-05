<?php

use App\Models\Carousels;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('department detail page can be rendered with content', function () {
    $department = Department::factory()->create([
        'name' => 'Computer Science',
        'slug' => 'computer-science',
        'content' => '<p>Welcome to CS department.</p>',
    ]);

    $response = $this->get('/departments/computer-science');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Departments')
        ->where('department.name', 'Computer Science')
        ->where('department.content', '<p>Welcome to CS department.</p>')
    );
});

test('department detail page contains carousels relation', function () {
    $department = Department::factory()->create([
        'name' => 'Engineering',
        'slug' => 'engineering',
    ]);

    Carousels::create([
        'title' => 'Engineering Slide',
        'description' => 'A slide about engineering',
        'image_url' => 'engineering.jpg',
        'department_id' => $department->id,
        'order' => 1,
    ]);

    $response = $this->get('/departments/engineering');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Departments')
        ->where('department.carousels.0.title', 'Engineering Slide')
    );
});
