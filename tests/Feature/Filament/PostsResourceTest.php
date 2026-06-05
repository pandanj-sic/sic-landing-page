<?php

use App\Enums\PostType;
use App\Filament\Resources\Posts\Pages\CreatePosts;
use App\Filament\Resources\Posts\Pages\EditPosts;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\PostsResource;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can render list page', function () {
    $this->get(PostsResource::getUrl('index'))
        ->assertSuccessful();
});

test('can list posts', function () {
    $posts = Posts::factory()->count(3)->create();

    Livewire\Livewire::test(ListPosts::class)
        ->assertCanSeeTableRecords($posts);
});

test('can render create page', function () {
    $this->get(PostsResource::getUrl('create'))
        ->assertSuccessful();
});

test('can create an article post', function () {
    $newData = Posts::factory()->make();

    Livewire\Livewire::test(CreatePosts::class)
        ->fillForm([
            'title' => $newData->title,
            'content' => $newData->content,
            'type' => PostType::Article->value,
            'is_published' => false,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Posts::class, [
        'title' => $newData->title,
        'type' => PostType::Article->value,
    ]);
});

test('can create an event post with event fields', function () {
    $newData = Posts::factory()->event()->make();

    Livewire\Livewire::test(CreatePosts::class)
        ->fillForm([
            'title' => $newData->title,
            'content' => $newData->content,
            'type' => PostType::Event->value,
            'start_date' => $newData->start_date,
            'end_date' => $newData->end_date,
            'location' => $newData->location,
            'is_published' => false,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Posts::class, [
        'title' => $newData->title,
        'type' => PostType::Event->value,
        'location' => $newData->location,
    ]);
});

test('can validate create form requires title', function () {
    Livewire\Livewire::test(CreatePosts::class)
        ->fillForm([
            'title' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});

test('can validate create form requires type', function () {
    Livewire\Livewire::test(CreatePosts::class)
        ->fillForm([
            'type' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['type' => 'required']);
});

test('can render edit page', function () {
    $post = Posts::factory()->create();

    $this->get(PostsResource::getUrl('edit', ['record' => $post]))
        ->assertSuccessful();
});

test('can update a post', function () {
    $post = Posts::factory()->create();
    $newData = Posts::factory()->make();

    Livewire\Livewire::test(EditPosts::class, ['record' => $post->getRouteKey()])
        ->fillForm([
            'title' => $newData->title,
            'content' => $newData->content,
            'type' => PostType::Announcement->value,
            'is_published' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $post->refresh();
    expect($post->title)->toBe($newData->title);
    expect($post->type)->toBe(PostType::Announcement);
    expect($post->is_published)->toBeTrue();
});

test('can update a post to event type with event fields', function () {
    $post = Posts::factory()->create();

    Livewire\Livewire::test(EditPosts::class, ['record' => $post->getRouteKey()])
        ->fillForm([
            'type' => PostType::Event->value,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-15',
            'location' => 'Main Hall',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $post->refresh();
    expect($post->type)->toBe(PostType::Event);
    expect($post->location)->toBe('Main Hall');
    expect($post->start_date->format('Y-m-d'))->toBe('2026-06-01');
});

test('can delete a post', function () {
    $post = Posts::factory()->create();

    Livewire\Livewire::test(EditPosts::class, ['record' => $post->getRouteKey()])
        ->callAction('delete');

    $this->assertModelMissing($post);
});
