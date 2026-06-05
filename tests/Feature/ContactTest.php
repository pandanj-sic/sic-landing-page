<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('contact page can be rendered', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Contact'));
});

test('contact form can be submitted successfully', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Inquiry about Admission',
        'message' => 'Hello, I would like to ask about the enrollment period for next semester.',
    ];

    $response = $this->post('/contact', $data);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Your message has been sent successfully! We will get back to you soon.');

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Inquiry about Admission',
        'message' => 'Hello, I would like to ask about the enrollment period for next semester.',
        'is_read' => false,
    ]);
});

test('contact form validation fails with invalid data', function () {
    $data = [
        'name' => '',
        'email' => 'invalid-email',
        'subject' => 'Test',
        'message' => 'Short',
    ];

    $response = $this->post('/contact', $data);

    $response->assertSessionHasErrors(['name', 'email', 'message']);
    $this->assertDatabaseCount('contact_messages', 0);
});
