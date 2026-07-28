<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselItem;
use App\Models\Department;
use App\Models\Download;
use App\Models\DownloadDirectory;
use App\Models\HeroSection;
use App\Models\Member;
use App\Models\NavigationLink;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Dashboard Overview
     */
    public function dashboard(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $postsQuery = Post::query();
        $downloadsQuery = Download::query();

        if ($user->isDepartmentUser()) {
            $postsQuery->where('department_id', $user->department_id);
            $downloadsQuery->where('department_id', $user->department_id);
        }

        return Inertia::render('admin/Dashboard', [
            'postsCount' => $postsQuery->count(),
            'downloadsCount' => $downloadsQuery->count(),
            'departmentsCount' => Department::count(),
            'usersCount' => User::count(),
        ]);
    }

    /**
     * POSTS MANAGEMENT
     */
    public function postsIndex(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $query = Post::with(['department', 'user']);

        if ($user->isDepartmentUser()) {
            $query->where('department_id', $user->department_id);
        }

        return Inertia::render('admin/posts/Index', [
            'posts' => $query->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function postsCreate(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/posts/Create', [
            'departments' => $departments,
        ]);
    }

    public function postsStore(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', 'in:article,event,announcement'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'highlight_image' => ['nullable', 'image', 'max:5120'], // 5MB max
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_featured' => ['boolean'],
        ]);

        if ($user->isDepartmentUser()) {
            $validated['department_id'] = $user->department_id;
        }

        $imagePath = null;
        if ($request->hasFile('highlight_image')) {
            $imagePath = $request->file('highlight_image')->store('posts', 'public');
        }

        $slug = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        if (empty($slug)) {
            $slug = 'post';
        }
        $originalSlug = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        Post::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'highlight_image_path' => $imagePath ? '/storage/'.$imagePath : null,
            'department_id' => $validated['department_id'],
            'user_id' => $user->id,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    public function postsEdit(Request $request, Post $post): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $post->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/posts/Edit', [
            'post' => $post,
            'departments' => $departments,
        ]);
    }

    public function postsUpdate(Request $request, Post $post): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $post->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug,'.$post->id],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', 'in:article,event,announcement'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'highlight_image' => ['nullable', 'image', 'max:5120'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_featured' => ['boolean'],
        ]);

        if ($user->isDepartmentUser()) {
            $validated['department_id'] = $user->department_id;
        }

        if ($request->hasFile('highlight_image')) {
            if ($post->highlight_image_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $post->highlight_image_path));
            }
            $imagePath = $request->file('highlight_image')->store('posts', 'public');
            $post->highlight_image_path = '/storage/'.$imagePath;
        }

        $slug = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        if (empty($slug)) {
            $slug = 'post';
        }
        $originalSlug = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $post->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'department_id' => $validated['department_id'],
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    public function postsDelete(Request $request, Post $post): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $post->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($post->highlight_image_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $post->highlight_image_path));
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }

    /**
     * INLINE IMAGE UPLOAD FOR MDX EDITOR
     */
    public function uploadInlineImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('image')->store('posts/inline', 'public');

        return response()->json([
            'url' => '/storage/'.$path,
        ]);
    }

    /**
     * DOWNLOADS MANAGEMENT
     */
    public function downloadsIndex(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $query = Download::with('department');

        if ($user->isDepartmentUser()) {
            $query->where('department_id', $user->department_id);
        }

        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/downloads/Index', [
            'downloads' => $query->orderBy('created_at', 'desc')->get(),
            'departments' => $departments,
            'customDirectories' => DownloadDirectory::all(),
        ]);
    }

    public function downloadsStore(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'file_name' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:20480'], // 20MB max
            'tags' => ['nullable', 'string'],
            'directory' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        if ($user->isDepartmentUser()) {
            $validated['department_id'] = $user->department_id;
        }

        $filePath = $request->file('file')->store('downloads', 'public');
        $fileSize = $request->file('file')->getSize();

        // Process tags
        $tagsArray = [];
        if (! empty($validated['tags'])) {
            $tagsArray = array_map('trim', explode(',', $validated['tags']));
        }

        Download::create([
            'file_name' => $validated['file_name'],
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'tags' => $tagsArray,
            'directory' => $validated['directory'] ?? 'General',
            'department_id' => $validated['department_id'],
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.downloads.index')->with('success', 'File uploaded successfully.');
    }

    public function downloadsDelete(Request $request, Download $download): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $download->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        Storage::disk('public')->delete($download->file_path);
        $download->delete();

        return redirect()->route('admin.downloads.index')->with('success', 'File deleted successfully.');
    }

    public function directoriesStore(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        if ($user->isDepartmentUser()) {
            $validated['department_id'] = $user->department_id;
        }

        DownloadDirectory::create([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Folder created successfully.');
    }

    public function directoriesDelete(Request $request, DownloadDirectory $directory): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $directory->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $directory->delete();

        return redirect()->back()->with('success', 'Folder deleted.');
    }

    /**
     * NAVIGATION LINKS MANAGEMENT (SuperAdmin Only)
     */
    public function navlinksIndex(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/navlinks/Index', [
            'navigationLinks' => NavigationLink::with('parent')->orderBy('order')->get(),
        ]);
    }

    public function navlinksStore(Request $request): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer'],
            'is_active' => ['boolean'],
            'parent_id' => ['nullable', 'exists:navigation_links,id'],
        ]);

        NavigationLink::create($validated);

        return redirect()->route('admin.navlinks.index')->with('success', 'Navigation link created.');
    }

    public function navlinksUpdate(Request $request, NavigationLink $navigationLink): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer'],
            'is_active' => ['boolean'],
            'parent_id' => ['nullable', 'exists:navigation_links,id'],
        ]);

        $navigationLink->update($validated);

        return redirect()->route('admin.navlinks.index')->with('success', 'Navigation link updated.');
    }

    public function navlinksDelete(Request $request, NavigationLink $navigationLink): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $navigationLink->delete();

        return redirect()->route('admin.navlinks.index')->with('success', 'Navigation link deleted.');
    }

    /**
     * DEPARTMENTS & ORG CHART MANAGEMENT
     */
    public function departmentsIndex(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $query = Department::query();
        if ($user->isDepartmentUser()) {
            $query->where('id', $user->department_id);
        }

        return Inertia::render('admin/departments/Index', [
            'departments' => $query->get(),
        ]);
    }

    public function departmentsCreate(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/departments/Create');
    }

    public function departmentsEdit(Request $request, Department $department): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $department->id !== $user->department_id) {
            abort(403);
        }

        return Inertia::render('admin/departments/Edit', [
            'department' => $department,
        ]);
    }

    public function departmentsUpdate(Request $request, Department $department): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $department->id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hero_content' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($department->logo_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $department->logo_path));
            }
            $logoPath = $request->file('logo')->store('departments', 'public');
            $department->logo_path = '/storage/'.$logoPath;
        }

        $department->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'hero_content' => $validated['hero_content'],
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Department details updated.');
    }

    public function departmentsStore(Request $request): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string'],
            'hero_content' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('departments', 'public');
            $logoPath = '/storage/'.$path;
        }

        Department::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'hero_content' => $validated['hero_content'] ?? null,
            'logo_path' => $logoPath,
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
    }

    public function departmentsDelete(Request $request, Department $department): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($department->logo_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $department->logo_path));
        }

        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully.');
    }

    /**
     * MEMBERS & ORG CHART
     */
    public function membersIndex(Request $request, Department $department): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $department->id !== $user->department_id) {
            abort(403);
        }

        return Inertia::render('admin/departments/Members', [
            'department' => $department,
            'members' => Member::where('department_id', $department->id)->orderBy('order')->get(),
        ]);
    }

    public function membersStore(Request $request, Department $department): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $department->id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:members,id'],
            'order' => ['required', 'integer'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('members', 'public');
        }

        Member::create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'department_id' => $department->id,
            'parent_id' => $validated['parent_id'],
            'order' => $validated['order'],
            'image_path' => $imagePath ? '/storage/'.$imagePath : null,
        ]);

        return redirect()->route('admin.departments.members', $department->id)->with('success', 'Member added to organization chart.');
    }

    public function membersUpdate(Request $request, Department $department, Member $member): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $department->id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:members,id'],
            'order' => ['required', 'integer'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($member->image_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $member->image_path));
            }
            $imagePath = $request->file('image')->store('members', 'public');
            $member->image_path = '/storage/'.$imagePath;
        }

        $member->update([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'parent_id' => $validated['parent_id'],
            'order' => $validated['order'],
        ]);

        return redirect()->route('admin.departments.members', $department->id)->with('success', 'Member updated.');
    }

    public function membersDelete(Request $request, Department $department, Member $member): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $department->id !== $user->department_id) {
            abort(403);
        }

        if ($member->image_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $member->image_path));
        }

        $member->delete();

        return redirect()->route('admin.departments.members', $department->id)->with('success', 'Member removed.');
    }

    /**
     * CAROUSEL MANAGEMENT
     */
    public function carouselsIndex(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $query = CarouselItem::with('department');
        if ($user->isDepartmentUser()) {
            $query->where('department_id', $user->department_id);
        }

        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/carousels/Index', [
            'carouselItems' => $query->orderBy('order')->get(),
            'departments' => $departments,
        ]);
    }

    public function carouselsStore(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'order' => ['required', 'integer'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'show_on_main' => ['nullable', 'boolean'],
        ]);

        if ($user->isDepartmentUser()) {
            $validated['department_id'] = $user->department_id;
        }

        $imagePath = $request->file('image')->store('carousels', 'public');

        CarouselItem::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'image_path' => '/storage/'.$imagePath,
            'link_url' => $validated['link_url'],
            'order' => $validated['order'],
            'department_id' => $validated['department_id'],
            'show_on_main' => $request->boolean('show_on_main', true),
        ]);

        return redirect()->route('admin.carousels.index')->with('success', 'Carousel slide added.');
    }

    public function carouselsDelete(Request $request, CarouselItem $carouselItem): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $carouselItem->department_id !== $user->department_id) {
            abort(403);
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $carouselItem->image_path));
        $carouselItem->delete();

        return redirect()->route('admin.carousels.index')->with('success', 'Carousel slide deleted.');
    }

    public function carouselsEdit(Request $request, CarouselItem $carouselItem): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $carouselItem->department_id !== $user->department_id) {
            abort(403);
        }

        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/carousels/Edit', [
            'carouselItem' => $carouselItem,
            'departments' => $departments,
        ]);
    }

    public function carouselsUpdate(Request $request, CarouselItem $carouselItem): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isDepartmentUser() && $carouselItem->department_id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'order' => ['required', 'integer'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'show_on_main' => ['nullable', 'boolean'],
        ]);

        if ($user->isDepartmentUser()) {
            $validated['department_id'] = $user->department_id;
        }

        if ($request->hasFile('image')) {
            if ($carouselItem->image_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $carouselItem->image_path));
            }
            $imagePath = $request->file('image')->store('carousels', 'public');
            $carouselItem->image_path = '/storage/'.$imagePath;
        }

        $carouselItem->update([
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'order' => $validated['order'],
            'department_id' => $validated['department_id'] ?? null,
            'show_on_main' => $request->boolean('show_on_main'),
            'image_path' => $carouselItem->image_path,
        ]);

        return redirect()->route('admin.carousels.index')->with('success', 'Carousel slide updated.');
    }

    /**
     * USERS MANAGEMENT (SuperAdmin Only)
     */
    public function usersIndex(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/users/Index', [
            'users' => User::with('department')->get(),
            'departments' => Department::all(),
        ]);
    }

    public function usersStore(Request $request): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:superadmin,department_user'],
            'department_id' => ['nullable', 'required_if:role,department_user', 'exists:departments,id'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['role'] === 'department_user' ? $validated['department_id'] : null,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function usersUpdate(Request $request, User $user): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:superadmin,department_user'],
            'department_id' => ['nullable', 'required_if:role,department_user', 'exists:departments,id'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['role'] === 'department_user' ? $validated['department_id'] : null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function usersDelete(Request $request, User $user): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete current logged in user.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function downloadsCreate(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/downloads/Create', [
            'departments' => $departments,
            'customDirectories' => DownloadDirectory::all(),
        ]);
    }

    public function carouselsCreate(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/carousels/Create', ['departments' => $departments]);
    }

    public function usersCreate(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/users/Create', ['departments' => Department::all()]);
    }

    public function usersEdit(Request $request, User $user): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/users/Edit', [
            'user' => $user,
            'departments' => Department::all(),
        ]);
    }

    public function navlinksCreate(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/navlinks/Create', [
            'parentLinks' => NavigationLink::whereNull('parent_id')->orderBy('order')->get(),
        ]);
    }

    public function navlinksEdit(Request $request, NavigationLink $navigationLink): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/navlinks/Edit', [
            'navigationLink' => $navigationLink,
            'parentLinks' => NavigationLink::whereNull('parent_id')->where('id', '!=', $navigationLink->id)->orderBy('order')->get(),
        ]);
    }

    public function membersCreate(Request $request, Department $department): Response
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->isDepartmentUser() && $department->id !== $user->department_id) {
            abort(403);
        }

        return Inertia::render('admin/departments/members/Create', [
            'department' => $department,
            'members' => Member::where('department_id', $department->id)->orderBy('order')->get(),
        ]);
    }

    public function membersEdit(Request $request, Department $department, Member $member): Response
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->isDepartmentUser() && $department->id !== $user->department_id) {
            abort(403);
        }

        return Inertia::render('admin/departments/members/Edit', [
            'department' => $department,
            'member' => $member,
            'members' => Member::where('department_id', $department->id)->where('id', '!=', $member->id)->orderBy('order')->get(),
        ]);
    }

    public function landingSettings(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/settings/Landing', [
            'welcomeHeroContent' => Setting::getByKey('welcome_hero_content', ''),
        ]);
    }

    public function landingSettingsUpdate(Request $request): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'welcome_hero_content' => ['nullable', 'string'],
        ]);

        Setting::setByKey('welcome_hero_content', $validated['welcome_hero_content'] ?? '');

        return redirect()->back()->with('success', 'Landing page settings updated successfully.');
    }

    public function heroSectionsIndex(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->isDepartmentUser()) {
            $heroSections = HeroSection::where('department_id', $user->department_id)
                ->with('department')
                ->orderBy('order')
                ->get();
        } else {
            $heroSections = HeroSection::with('department')
                ->orderBy('order')
                ->get();
        }

        return Inertia::render('admin/hero-sections/Index', [
            'heroSections' => $heroSections,
        ]);
    }

    public function heroSectionsCreate(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/hero-sections/Create', [
            'departments' => $departments,
        ]);
    }

    public function heroSectionsStore(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'order' => ['required', 'integer'],
            'is_active' => ['boolean'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        if ($user->isDepartmentUser()) {
            $validated['department_id'] = $user->department_id;
        }

        HeroSection::create($validated);

        return redirect()->route('admin.hero-sections.index')->with('success', 'Hero section created successfully.');
    }

    public function heroSectionsEdit(Request $request, HeroSection $heroSection): Response
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->isDepartmentUser() && $heroSection->department_id !== $user->department_id) {
            abort(403);
        }

        $departments = $user->isSuperAdmin()
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return Inertia::render('admin/hero-sections/Edit', [
            'heroSection' => $heroSection,
            'departments' => $departments,
        ]);
    }

    public function heroSectionsUpdate(Request $request, HeroSection $heroSection): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->isDepartmentUser() && $heroSection->department_id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'order' => ['required', 'integer'],
            'is_active' => ['boolean'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        if ($user->isDepartmentUser()) {
            $validated['department_id'] = $user->department_id;
        }

        $heroSection->update($validated);

        return redirect()->route('admin.hero-sections.index')->with('success', 'Hero section updated successfully.');
    }

    public function heroSectionsDelete(Request $request, HeroSection $heroSection): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->isDepartmentUser() && $heroSection->department_id !== $user->department_id) {
            abort(403);
        }

        $heroSection->delete();

        return redirect()->route('admin.hero-sections.index')->with('success', 'Hero section deleted successfully.');
    }
}
