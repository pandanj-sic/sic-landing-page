<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Department/Index', [
            'departments' => Department::with(['members', 'posts', 'carousels'])->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Department/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // No custom fields present in departments table yet
        ]);

        Department::create($validated);

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['members' => function ($query) {
            $query->orderBy('order');
        }, 'carousels' => function ($query) {
            $query->orderBy('order');
        }]);

        $posts = $department->posts()
            ->where('is_published', true)
            ->with('tags:id,name')
            ->latest()
            ->take(6)
            ->get(['id', 'title', 'content', 'department_id', 'image_url', 'type', 'created_at']);

        return Inertia::render('Departments', [
            'department' => $department,
            'posts' => $posts,
        ]);
    }

    public function edit(Department $department)
    {
        return Inertia::render('Department/Edit', [
            'department' => $department,
        ]);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            // No custom fields present in departments table yet
        ]);

        $department->update($validated);

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully.');
    }
}
