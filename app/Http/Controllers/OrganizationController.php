<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    public function index(): Response
    {
        $departments = Department::with('members')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'image_url']);

        return Inertia::render('Organization', [
            'departments' => $departments,
        ]);
    }
}
