<?php

use App\Models\Scholarship;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('scholarships page can be rendered', function () {
    $response = $this->get('/scholarships');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Scholarships'));
});

test('scholarships page displays active scholarships from database', function () {
    Scholarship::create([
        'name' => 'Active Test Scholarship',
        'coverage' => '100%',
        'description' => 'Active description',
        'requirements' => 'Active requirements',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    Scholarship::create([
        'name' => 'Inactive Test Scholarship',
        'coverage' => '50%',
        'description' => 'Inactive description',
        'requirements' => 'Inactive requirements',
        'is_active' => false,
        'sort_order' => 2,
    ]);

    $response = $this->get('/scholarships');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Scholarships')
        ->has('scholarships', 1)
        ->where('scholarships.0.name', 'Active Test Scholarship')
        ->where('scholarships.0.coverage', '100%')
        ->where('scholarships.0.description', 'Active description')
        ->where('scholarships.0.requirements', 'Active requirements')
    );
});
