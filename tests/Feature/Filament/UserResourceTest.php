<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can render list page', function () {
    $this->get(UserResource::getUrl('index'))
        ->assertSuccessful();
});

test('can list users', function () {
    $users = User::factory()->count(3)->create();

    Livewire\Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords($users);
});

test('can render create page', function () {
    $this->get(UserResource::getUrl('create'))
        ->assertSuccessful();
});

test('can create a user', function () {
    Livewire\Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

test('can render edit page', function () {
    $targetUser = User::factory()->create();

    $this->get(UserResource::getUrl('edit', ['record' => $targetUser]))
        ->assertSuccessful();
});

test('can update a user', function () {
    $targetUser = User::factory()->create(['name' => 'Old Name']);

    Livewire\Livewire::test(EditUser::class, ['record' => $targetUser->getRouteKey()])
        ->fillForm([
            'name' => 'New Name',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $targetUser->refresh();
    expect($targetUser->name)->toBe('New Name');
});

test('can delete a user', function () {
    $targetUser = User::factory()->create();

    Livewire\Livewire::test(EditUser::class, ['record' => $targetUser->getRouteKey()])
        ->callAction('delete');

    $this->assertModelMissing($targetUser);
});
