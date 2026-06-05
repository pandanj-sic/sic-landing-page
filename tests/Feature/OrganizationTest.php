<?php

use App\Models\Department;
use App\Models\DepartmentMember;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('organization page can be rendered', function () {
    $response = $this->get('/organization');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Organization'));
});

test('organization page displays departments with members', function () {
    $department = Department::factory()->create(['name' => 'Test Department']);
    $member = DepartmentMember::factory()->create([
        'department_id' => $department->id,
        'name' => 'John Doe',
        'position' => 'Professor',
    ]);

    $response = $this->get('/organization');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Organization')
        ->has('departments', 1)
        ->where('departments.0.name', 'Test Department')
        ->has('departments.0.members', 1)
        ->where('departments.0.members.0.name', 'John Doe')
        ->where('departments.0.members.0.position', 'Professor')
    );
});
