<?php

use App\Filament\Resources\Admissions\AdmissionResource;
use App\Filament\Resources\Admissions\Pages\CreateAdmission;
use App\Filament\Resources\Admissions\Pages\EditAdmission;
use App\Filament\Resources\Admissions\Pages\ListAdmissions;
use App\Models\Admission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can render list page', function () {
    $this->get(AdmissionResource::getUrl('index'))
        ->assertSuccessful();
});

test('can list admissions', function () {
    $admissions = Admission::factory()->count(3)->create();

    Livewire\Livewire::test(ListAdmissions::class)
        ->assertCanSeeTableRecords($admissions);
});

test('can render create page', function () {
    $this->get(AdmissionResource::getUrl('create'))
        ->assertSuccessful();
});

test('can create an admission section', function () {
    Livewire\Livewire::test(CreateAdmission::class)
        ->fillForm([
            'title' => 'Requirements',
            'content' => '<p>Test content</p>',
            'is_active' => true,
            'sort_order' => 10,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Admission::class, [
        'title' => 'Requirements',
        'is_active' => true,
        'sort_order' => 10,
    ]);
});

test('can render edit page', function () {
    $admission = Admission::factory()->create();

    $this->get(AdmissionResource::getUrl('edit', ['record' => $admission]))
        ->assertSuccessful();
});

test('can update an admission section', function () {
    $admission = Admission::factory()->create(['title' => 'Old Title']);

    Livewire\Livewire::test(EditAdmission::class, ['record' => $admission->getRouteKey()])
        ->fillForm([
            'title' => 'New Title',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $admission->refresh();
    expect($admission->title)->toBe('New Title');
});

test('can delete an admission section', function () {
    $admission = Admission::factory()->create();

    Livewire\Livewire::test(EditAdmission::class, ['record' => $admission->getRouteKey()])
        ->callAction('delete');

    $this->assertModelMissing($admission);
});
