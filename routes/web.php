<?php

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/departments/{department:slug}', [PublicController::class, 'department'])->name('departments.show');
Route::get('/downloads', [PublicController::class, 'downloads'])->name('downloads.index');
Route::get('/posts', [PublicController::class, 'posts'])->name('posts.index');
Route::get('/posts/{slug}', [PublicController::class, 'post'])->name('posts.show');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');

use App\Http\Controllers\Admin\AdminController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Posts
    Route::get('/admin/posts', [AdminController::class, 'postsIndex'])->name('admin.posts.index');
    Route::get('/admin/posts/create', [AdminController::class, 'postsCreate'])->name('admin.posts.create');
    Route::post('/admin/posts', [AdminController::class, 'postsStore'])->name('admin.posts.store');
    Route::get('/admin/posts/{post}/edit', [AdminController::class, 'postsEdit'])->name('admin.posts.edit');
    Route::post('/admin/posts/{post}', [AdminController::class, 'postsUpdate'])->name('admin.posts.update');
    Route::delete('/admin/posts/{post}', [AdminController::class, 'postsDelete'])->name('admin.posts.delete');
    Route::post('/admin/posts/upload-image', [AdminController::class, 'uploadInlineImage'])->name('admin.posts.upload_image');

    // Downloads
    Route::get('/admin/downloads', [AdminController::class, 'downloadsIndex'])->name('admin.downloads.index');
    Route::get('/admin/downloads/create', [AdminController::class, 'downloadsCreate'])->name('admin.downloads.create');
    Route::post('/admin/downloads', [AdminController::class, 'downloadsStore'])->name('admin.downloads.store');
    Route::delete('/admin/downloads/{download}', [AdminController::class, 'downloadsDelete'])->name('admin.downloads.delete');
    Route::post('/admin/downloads/directories', [AdminController::class, 'directoriesStore'])->name('admin.downloads.directories.store');
    Route::delete('/admin/downloads/directories/{directory}', [AdminController::class, 'directoriesDelete'])->name('admin.downloads.directories.delete');

    // Navigation Links
    Route::get('/admin/navigation-links', [AdminController::class, 'navlinksIndex'])->name('admin.navlinks.index');
    Route::get('/admin/navigation-links/create', [AdminController::class, 'navlinksCreate'])->name('admin.navlinks.create');
    Route::post('/admin/navigation-links', [AdminController::class, 'navlinksStore'])->name('admin.navlinks.store');
    Route::get('/admin/navigation-links/{navigationLink}/edit', [AdminController::class, 'navlinksEdit'])->name('admin.navlinks.edit');
    Route::put('/admin/navigation-links/{navigationLink}', [AdminController::class, 'navlinksUpdate'])->name('admin.navlinks.update');
    Route::delete('/admin/navigation-links/{navigationLink}', [AdminController::class, 'navlinksDelete'])->name('admin.navlinks.delete');

    // Departments
    Route::get('/admin/departments', [AdminController::class, 'departmentsIndex'])->name('admin.departments.index');
    Route::get('/admin/departments/create', [AdminController::class, 'departmentsCreate'])->name('admin.departments.create');
    Route::post('/admin/departments', [AdminController::class, 'departmentsStore'])->name('admin.departments.store');
    Route::get('/admin/departments/{department}/edit', [AdminController::class, 'departmentsEdit'])->name('admin.departments.edit');
    Route::post('/admin/departments/{department}', [AdminController::class, 'departmentsUpdate'])->name('admin.departments.update');
    Route::delete('/admin/departments/{department}', [AdminController::class, 'departmentsDelete'])->name('admin.departments.delete');

    // Members
    Route::get('/admin/departments/{department}/members', [AdminController::class, 'membersIndex'])->name('admin.departments.members');
    Route::get('/admin/departments/{department}/members/create', [AdminController::class, 'membersCreate'])->name('admin.departments.members.create');
    Route::post('/admin/departments/{department}/members', [AdminController::class, 'membersStore'])->name('admin.departments.members.store');
    Route::get('/admin/departments/{department}/members/{member}/edit', [AdminController::class, 'membersEdit'])->name('admin.departments.members.edit');
    Route::post('/admin/departments/{department}/members/{member}', [AdminController::class, 'membersUpdate'])->name('admin.departments.members.update');
    Route::delete('/admin/departments/{department}/members/{member}', [AdminController::class, 'membersDelete'])->name('admin.departments.members.delete');

    // Carousels
    Route::get('/admin/carousels', [AdminController::class, 'carouselsIndex'])->name('admin.carousels.index');
    Route::get('/admin/carousels/create', [AdminController::class, 'carouselsCreate'])->name('admin.carousels.create');
    Route::post('/admin/carousels', [AdminController::class, 'carouselsStore'])->name('admin.carousels.store');
    Route::get('/admin/carousels/{carouselItem}/edit', [AdminController::class, 'carouselsEdit'])->name('admin.carousels.edit');
    Route::post('/admin/carousels/{carouselItem}', [AdminController::class, 'carouselsUpdate'])->name('admin.carousels.update');
    Route::delete('/admin/carousels/{carouselItem}', [AdminController::class, 'carouselsDelete'])->name('admin.carousels.delete');

    // Users
    Route::get('/admin/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminController::class, 'usersCreate'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'usersStore'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'usersUpdate'])->name('admin.users.update');
    // Landing Page Settings
    Route::get('/admin/settings/landing', [AdminController::class, 'landingSettings'])->name('admin.settings.landing');
    Route::post('/admin/settings/landing', [AdminController::class, 'landingSettingsUpdate'])->name('admin.settings.landing.update');

    // Hero Sections
    Route::get('/admin/hero-sections', [AdminController::class, 'heroSectionsIndex'])->name('admin.hero-sections.index');
    Route::get('/admin/hero-sections/create', [AdminController::class, 'heroSectionsCreate'])->name('admin.hero-sections.create');
    Route::post('/admin/hero-sections', [AdminController::class, 'heroSectionsStore'])->name('admin.hero-sections.store');
    Route::get('/admin/hero-sections/{heroSection}/edit', [AdminController::class, 'heroSectionsEdit'])->name('admin.hero-sections.edit');
    Route::post('/admin/hero-sections/{heroSection}', [AdminController::class, 'heroSectionsUpdate'])->name('admin.hero-sections.update');
    Route::delete('/admin/hero-sections/{heroSection}', [AdminController::class, 'heroSectionsDelete'])->name('admin.hero-sections.delete');
});

require __DIR__.'/settings.php';
