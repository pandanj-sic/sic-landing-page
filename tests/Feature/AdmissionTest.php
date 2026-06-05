<?php

use App\Models\Admission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admission page can be rendered', function () {
    $response = $this->get('/admission');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Admission'));
});

test('admission page displays active sections from database', function () {
    $activeSection = Admission::factory()->create([
        'title' => 'Required Documents',
        'content' => '<p>Please submit birth certificate.</p>',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $inactiveSection = Admission::factory()->create([
        'title' => 'Hidden Process',
        'is_active' => false,
    ]);

    $response = $this->get('/admission');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Admission')
        ->has('sections', 1)
        ->where('sections.0.title', 'Required Documents')
        ->where('sections.0.content', '<p>Please submit birth certificate.</p>')
    );
});

test('admissions route redirects to admission', function () {
    $response = $this->get('/admissions');

    $response->assertRedirect('/admission');
});
