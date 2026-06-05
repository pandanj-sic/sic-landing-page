<?php

namespace App\Http\Controllers;

use App\Models\DepartmentMember;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentMemberController extends Controller
{
    public function index()
    {
        return Inertia::render('DepartmentMember/Index', [
            'members' => DepartmentMember::with('department')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('DepartmentMember/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:255',
        ]);

        DepartmentMember::create($validated);

        return redirect()->back()->with('success', 'Member created successfully.');
    }

    public function show(DepartmentMember $departmentMember)
    {
        return Inertia::render('DepartmentMember/Show', [
            'member' => $departmentMember->load('department'),
        ]);
    }

    public function edit(DepartmentMember $departmentMember)
    {
        return Inertia::render('DepartmentMember/Edit', [
            'member' => $departmentMember,
        ]);
    }

    public function update(Request $request, DepartmentMember $departmentMember)
    {
        $validated = $request->validate([
            'department_id' => 'sometimes|required|exists:departments,id',
            'name' => 'sometimes|required|string|max:255',
            'position' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:255',
        ]);

        $departmentMember->update($validated);

        return redirect()->back()->with('success', 'Member updated successfully.');
    }

    public function destroy(DepartmentMember $departmentMember)
    {
        $departmentMember->delete();

        return redirect()->back()->with('success', 'Member deleted successfully.');
    }
}
