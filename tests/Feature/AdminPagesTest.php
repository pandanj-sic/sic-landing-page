<?php

use App\Models\CarouselItem;
use App\Models\Department;
use App\Models\User;

test('unauthenticated users are redirected to login', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated administrators can access admin dashboard', function () {
    $user = User::factory()->create(['role' => 'superadmin']);

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);
});

test('authenticated administrators can access posts management', function () {
    $user = User::factory()->create(['role' => 'superadmin']);

    $response = $this->actingAs($user)->get('/admin/posts');
    $response->assertStatus(200);
});

test('authenticated administrators can access downloads management', function () {
    $user = User::factory()->create(['role' => 'superadmin']);

    $response = $this->actingAs($user)->get('/admin/downloads');
    $response->assertStatus(200);
});

test('authenticated department users can access posts management', function () {
    $user = User::factory()->create(['role' => 'department_user']);

    $response = $this->actingAs($user)->get('/admin/posts');
    $response->assertStatus(200);
});

test('authenticated administrators can access carousel edit page', function () {
    $user = User::factory()->create(['role' => 'superadmin']);
    $carouselItem = CarouselItem::create([
        'title' => 'Test Carousel',
        'image_path' => '/storage/carousels/test.jpg',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)->get("/admin/carousels/{$carouselItem->id}/edit");
    $response->assertStatus(200);
});

test('authenticated administrators can update carousel item', function () {
    $user = User::factory()->create(['role' => 'superadmin']);
    $carouselItem = CarouselItem::create([
        'title' => 'Original Title',
        'image_path' => '/storage/carousels/test.jpg',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)->post("/admin/carousels/{$carouselItem->id}", [
        'title' => 'Updated Title',
        'subtitle' => 'New Subtitle',
        'order' => 2,
    ]);

    $response->assertRedirect('/admin/carousels');
    $this->assertDatabaseHas('carousel_items', [
        'id' => $carouselItem->id,
        'title' => 'Updated Title',
        'subtitle' => 'New Subtitle',
        'order' => 2,
    ]);
});

test('authenticated administrators can access department create page', function () {
    $user = User::factory()->create(['role' => 'superadmin']);

    $response = $this->actingAs($user)->get('/admin/departments/create');
    $response->assertStatus(200);
});

test('authenticated administrators can access department edit page', function () {
    $user = User::factory()->create(['role' => 'superadmin']);
    $department = Department::create([
        'name' => 'Computer Science',
        'slug' => 'computer-science',
    ]);

    $response = $this->actingAs($user)->get("/admin/departments/{$department->id}/edit");
    $response->assertStatus(200);
});
