<?php

use App\Enums\PostType;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DownloadablesController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PostsController;
use App\Models\Admission;
use App\Models\Carousels;
use App\Models\HistoryMilestone;
use App\Models\MessageOfTheDay;
use App\Models\Posts;
use App\Models\Scholarship;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $carousels = Carousels::orderBy('order')
        ->get(['id', 'title', 'description', 'button_text', 'button_url', 'image_url', 'order']);

    $posts = Posts::where('is_published', true)
        ->where('type', PostType::Article)
        ->with('department:id,name')
        ->with('tags:id,name')
        ->latest()
        ->take(6)
        ->get(['id', 'title', 'content', 'department_id', 'image_url', 'type', 'created_at']);

    $announcements = Posts::where('is_published', true)
        ->where('type', PostType::Announcement)
        ->latest()
        ->take(5)
        ->get(['id', 'title', 'content', 'image_url', 'type', 'created_at']);

    $events = Posts::where('is_published', true)
        ->where('type', PostType::Event)
        ->where('end_date', '>=', now())
        ->orderBy('start_date')
        ->take(4)
        ->get(['id', 'title', 'content', 'image_url', 'type', 'start_date', 'end_date', 'location', 'created_at']);

    $messageOfTheDay = MessageOfTheDay::where('is_active', true)
        ->latest()
        ->first(['id', 'person_name', 'person_image', 'message']);

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'carousels' => $carousels,
        'posts' => $posts,
        'announcements' => $announcements,
        'events' => $events,
        'messageOfTheDay' => $messageOfTheDay,
    ]);
})->name('home');

Route::get('history', function () {
    $timeline = HistoryMilestone::orderBy('sort_order', 'asc')->get();

    return Inertia::render('History', [
        'timeline' => $timeline,
    ]);
})->name('history');
Route::get('scholarships', function () {
    $scholarships = Scholarship::where('is_active', true)
        ->orderBy('sort_order', 'asc')
        ->get();

    return Inertia::render('Scholarships', [
        'scholarships' => $scholarships,
    ]);
})->name('scholarships');
Route::inertia('mission-and-vision', 'MissionAndVision')->name('mission-and-vision');

Route::get('admission', function () {
    $sections = Admission::where('is_active', true)
        ->orderBy('sort_order', 'asc')
        ->get();

    return Inertia::render('Admission', [
        'sections' => $sections,
    ]);
})->name('admission');

Route::get('admissions', function () {
    return redirect()->route('admission');
});

Route::get('contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('contact-us', function () {
    return redirect()->route('contact.show');
});

Route::get('posts', [PostsController::class, 'index'])->name('posts.index');
Route::get('posts/{post}', [PostsController::class, 'show'])->name('posts.show');

Route::get('news', [PostsController::class, 'news'])->name('news.index');
Route::get('announcements', [PostsController::class, 'announcements'])->name('announcements.index');
Route::get('events', [PostsController::class, 'events'])->name('events.index');

Route::get('downloadables', [DownloadablesController::class, 'index'])->name('downloadables.index');
Route::get('downloadables/download/{file}', [DownloadablesController::class, 'download'])->name('downloadables.download');

Route::get('organization', [OrganizationController::class, 'index'])->name('organization.index');

Route::get('departments/{department:slug}', [DepartmentController::class, 'show'])->name('departments.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::match(['get', 'post'], 'login', function () {
    abort(404);
});
