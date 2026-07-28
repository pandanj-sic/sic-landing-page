<?php

use App\Models\Department;
use App\Models\Post;

beforeEach(function () {
    $this->seed();
});

test('home page returns successful response', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('downloads page returns successful response', function () {
    $response = $this->get('/downloads');
    $response->assertStatus(200);
});

test('posts page returns successful response', function () {
    $response = $this->get('/posts');
    $response->assertStatus(200);
});

test('department detail page returns successful response', function () {
    $department = Department::first();

    $response = $this->get("/departments/{$department->slug}");
    $response->assertStatus(200);
});

test('post detail page returns successful response', function () {
    $post = Post::first();

    $response = $this->get("/posts/{$post->slug}");
    $response->assertStatus(200);
});

test('contact page returns successful response', function () {
    $response = $this->get('/contact');
    $response->assertStatus(200);
});
