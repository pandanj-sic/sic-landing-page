<?php

namespace App\Filament\Resources\DownloadableFolders\Pages;

use App\Filament\Resources\DownloadableFolders\DownloadableFoldersResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDownloadableFolders extends ListRecords
{
    protected static string $resource = DownloadableFoldersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
