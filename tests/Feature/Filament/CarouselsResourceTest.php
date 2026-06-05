<?php

use App\Filament\Resources\Carousels\CarouselsResource;
use App\Filament\Resources\Carousels\Pages\CreateCarousels;
use App\Filament\Resources\Carousels\Pages\EditCarousels;
use App\Filament\Resources\Carousels\Pages\ListCarousels;
use App\Models\Carousels;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can render list page', function () {
    $this->get(CarouselsResource::getUrl('index'))
        ->assertSuccessful();
});

test('can list carousels', function () {
    $carousels = Carousels::factory()->count(3)->create();

    Livewire\Livewire::test(ListCarousels::class)
        ->assertCanSeeTableRecords($carousels);
});

test('can render create page', function () {
    $this->get(CarouselsResource::getUrl('create'))
        ->assertSuccessful();
});

test('can create a carousel', function () {
    $newData = Carousels::factory()->make();

    Livewire\Livewire::test(CreateCarousels::class)
        ->fillForm([
            'title' => $newData->title,
            'description' => $newData->description,
            'button_text' => $newData->button_text,
            'button_url' => $newData->button_url,
            'order' => $newData->order,
            'image_url' => [UploadedFile::fake()->image('carousel.jpg', 1920, 1080)],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Carousels::class, [
        'title' => $newData->title,
        'description' => $newData->description,
    ]);
});

test('can validate create form requires title', function () {
    Livewire\Livewire::test(CreateCarousels::class)
        ->fillForm([
            'title' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});

test('can render edit page', function () {
    $carousel = Carousels::factory()->create();

    $this->get(CarouselsResource::getUrl('edit', ['record' => $carousel]))
        ->assertSuccessful();
});

test('can update a carousel', function () {
    $carousel = Carousels::factory()->create();
    $newData = Carousels::factory()->make();

    Livewire\Livewire::test(EditCarousels::class, ['record' => $carousel->getRouteKey()])
        ->fillForm([
            'title' => $newData->title,
            'description' => $newData->description,
            'order' => $newData->order,
            'image_url' => [UploadedFile::fake()->image('updated.jpg', 1920, 1080)],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $carousel->refresh();
    expect($carousel->title)->toBe($newData->title);
    expect($carousel->description)->toBe($newData->description);
});

test('can delete a carousel', function () {
    $carousel = Carousels::factory()->create();

    Livewire\Livewire::test(EditCarousels::class, ['record' => $carousel->getRouteKey()])
        ->callAction('delete');

    $this->assertModelMissing($carousel);
});
