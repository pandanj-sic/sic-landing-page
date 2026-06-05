<?php

namespace App\Http\Controllers;

use App\Models\Carousels;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarouselsController extends Controller
{
    public function index()
    {
        return Inertia::render('Carousels/Index', [
            'carousels' => Carousels::with('department')->orderBy('order')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Carousels/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|string|max:255',
            'order' => 'integer',
            'department_id' => 'nullable|exists:departments,id',
            'image_url' => 'required|string|max:255',
        ]);

        Carousels::create($validated);

        return redirect()->back()->with('success', 'Carousel created successfully.');
    }

    public function show(Carousels $carousel)
    {
        return Inertia::render('Carousels/Show', [
            'carousel' => $carousel->load('department'),
        ]);
    }

    public function edit(Carousels $carousel)
    {
        return Inertia::render('Carousels/Edit', [
            'carousel' => $carousel,
        ]);
    }

    public function update(Request $request, Carousels $carousel)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|string|max:255',
            'order' => 'integer',
            'department_id' => 'nullable|exists:departments,id',
            'image_url' => 'sometimes|required|string|max:255',
        ]);

        $carousel->update($validated);

        return redirect()->back()->with('success', 'Carousel updated successfully.');
    }

    public function destroy(Carousels $carousel)
    {
        $carousel->delete();

        return redirect()->back()->with('success', 'Carousel deleted successfully.');
    }
}
