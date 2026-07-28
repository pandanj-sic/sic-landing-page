<?php

namespace App\Http\Controllers;

use App\Models\CarouselItem;
use App\Models\Department;
use App\Models\Download;
use App\Models\HeroSection;
use App\Models\Member;
use App\Models\NavigationLink;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    private function getNavLinks()
    {
        return NavigationLink::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
    }

    public function home(): Response
    {
        return Inertia::render('Welcome', [
            'carouselItems' => CarouselItem::where('show_on_main', true)->orderBy('order')->get(),
            'departments' => Department::all(),
            'featuredPosts' => Post::with('department')
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get(),
            'latestPosts' => Post::with('department')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get(),
            'navigationLinks' => $this->getNavLinks(),
            'welcomeHeroContent' => Setting::getByKey('welcome_hero_content'),
            'heroSections' => HeroSection::whereNull('department_id')->where('is_active', true)->orderBy('order')->get(),
        ]);
    }

    public function department(Department $department): Response
    {
        return Inertia::render('Department', [
            'department' => $department,
            'carouselItems' => CarouselItem::where('department_id', $department->id)->orderBy('order')->get(),
            'posts' => Post::where('department_id', $department->id)->orderBy('created_at', 'desc')->get(),
            // Eager load children recursively for organizational hierarchy chart
            'rootMembers' => Member::where('department_id', $department->id)
                ->whereNull('parent_id')
                ->with('children.children')
                ->orderBy('order')
                ->get(),
            'navigationLinks' => $this->getNavLinks(),
            'heroSections' => HeroSection::where('department_id', $department->id)->where('is_active', true)->orderBy('order')->get(),
        ]);
    }

    public function downloads(Request $request): Response
    {
        $query = Download::with('department');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%")
                    ->orWhere('directory', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Downloads', [
            'downloads' => $query->orderBy('created_at', 'desc')->get(),
            'departments' => Department::all(),
            'customDirectories' => \App\Models\DownloadDirectory::all(),
            'navigationLinks' => $this->getNavLinks(),
            'filters' => $request->only(['department_id', 'search']),
        ]);
    }

    public function posts(Request $request): Response
    {
        $query = Post::with(['department', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Posts', [
            'posts' => $query->orderBy('created_at', 'desc')->get(),
            'departments' => Department::all(),
            'navigationLinks' => $this->getNavLinks(),
            'filters' => $request->only(['type', 'department_id', 'search']),
        ]);
    }

    public function post(string $slug): Response
    {
        $post = Post::where('slug', $slug)->with(['department', 'user'])->firstOrFail();

        return Inertia::render('PostDetail', [
            'post' => $post,
            'departments' => Department::all(),
            'navigationLinks' => $this->getNavLinks(),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact', [
            'departments' => Department::all(),
            'navigationLinks' => $this->getNavLinks(),
        ]);
    }
}
