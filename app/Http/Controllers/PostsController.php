<?php

namespace App\Http\Controllers;

use App\Enums\PostType;
use App\Models\Department;
use App\Models\Posts;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostsController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Posts::query()
            ->where('is_published', true)
            ->with(['department:id,name', 'tags:id,name']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->integer('tag'));
            });
        }

        $sortField = match ($request->input('sort')) {
            'oldest' => ['created_at', 'asc'],
            'title' => ['title', 'asc'],
            default => ['created_at', 'desc'],
        };

        $query->orderBy($sortField[0], $sortField[1]);

        return Inertia::render('PostLists', [
            'posts' => $query->paginate(12)->withQueryString(),
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'tags' => Tag::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['search', 'type', 'department', 'tag', 'sort']),
        ]);
    }

    public function show(Posts $post): Response
    {
        abort_unless($post->is_published, 404);

        $post->load(['department:id,name', 'tags:id,name']);

        $relatedPosts = Posts::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->where(function ($query) use ($post) {
                $query->where('type', $post->type)
                    ->orWhere('department_id', $post->department_id);
            })
            ->with(['department:id,name', 'tags:id,name'])
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('Posts', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    public function news(Request $request): Response
    {
        $query = Posts::query()
            ->where('is_published', true)
            ->where('type', PostType::Article)
            ->with(['department:id,name', 'tags:id,name']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->integer('tag'));
            });
        }

        $sortField = match ($request->input('sort')) {
            'oldest' => ['created_at', 'asc'],
            'title' => ['title', 'asc'],
            default => ['created_at', 'desc'],
        };

        $query->orderBy($sortField[0], $sortField[1]);

        return Inertia::render('News', [
            'posts' => $query->paginate(12)->withQueryString(),
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'tags' => Tag::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['search', 'department', 'tag', 'sort']),
        ]);
    }

    public function announcements(Request $request): Response
    {
        $query = Posts::query()
            ->where('is_published', true)
            ->where('type', PostType::Announcement)
            ->with(['department:id,name', 'tags:id,name']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->integer('tag'));
            });
        }

        $sortField = match ($request->input('sort')) {
            'oldest' => ['created_at', 'asc'],
            'title' => ['title', 'asc'],
            default => ['created_at', 'desc'],
        };

        $query->orderBy($sortField[0], $sortField[1]);

        return Inertia::render('Announcements', [
            'posts' => $query->paginate(12)->withQueryString(),
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'tags' => Tag::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['search', 'department', 'tag', 'sort']),
        ]);
    }

    public function events(Request $request): Response
    {
        $query = Posts::query()
            ->where('is_published', true)
            ->where('type', PostType::Event)
            ->with(['department:id,name', 'tags:id,name']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->integer('tag'));
            });
        }

        $showPast = $request->boolean('show_past', false);
        if (! $showPast) {
            $query->where('end_date', '>=', now());
        }

        $sortField = match ($request->input('sort')) {
            'oldest' => ['start_date', 'asc'],
            'title' => ['title', 'asc'],
            default => ['start_date', 'desc'],
        };

        $query->orderBy($sortField[0], $sortField[1]);

        return Inertia::render('Events', [
            'posts' => $query->paginate(12)->withQueryString(),
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'tags' => Tag::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['search', 'department', 'tag', 'sort', 'show_past']),
        ]);
    }
}
