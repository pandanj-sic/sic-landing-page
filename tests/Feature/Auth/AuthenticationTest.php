<?php

use App\Models\User;

test('login screen is disabled and returns 404', function () {
    $response = $this->get('/login');

    $response->assertStatus(404);
});

test('login request is disabled and returns 404', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertStatus(404);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect(route('home'));
});
