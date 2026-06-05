<?php

use App\Models\HistoryMilestone;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('history page can be rendered', function () {
    $response = $this->get('/history');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('History'));
});

test('history page displays milestones from database', function () {
    HistoryMilestone::create([
        'year' => '2026',
        'title' => 'Test Milestone',
        'description' => 'This is a test milestone description.',
        'icon' => 'star',
        'sort_order' => 1,
    ]);

    $response = $this->get('/history');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('History')
        ->has('timeline', 1)
        ->where('timeline.0.year', '2026')
        ->where('timeline.0.title', 'Test Milestone')
        ->where('timeline.0.description', 'This is a test milestone description.')
        ->where('timeline.0.icon', 'star')
    );
});
