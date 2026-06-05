<?php

namespace App\Http\Controllers;

use App\Models\DownloadableFile;
use App\Models\DownloadableFolder;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadablesController extends Controller
{
    public function index(): Response
    {
        $folders = DownloadableFolder::query()
            ->whereNull('parent_id')
            ->with([
                'files' => fn ($query) => $query->orderBy('order'),
                'children' => fn ($query) => $query->orderBy('order')->with([
                    'files' => fn ($q) => $q->orderBy('order'),
                    'children' => fn ($q) => $q->orderBy('order')->with([
                        'files' => fn ($q2) => $q2->orderBy('order'),
                    ]),
                ]),
            ])
            ->orderBy('order')
            ->get();

        return Inertia::render('Downloadables', [
            'folders' => $folders,
        ]);
    }

    public function download(DownloadableFile $file): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return Storage::disk('public')->download($file->file_path, $file->name);
    }
}
