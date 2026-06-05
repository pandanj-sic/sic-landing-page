<?php

namespace App\Filament\Resources\DownloadableFolders\Pages;

use App\Filament\Resources\DownloadableFolders\DownloadableFoldersResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDownloadableFolders extends EditRecord
{
    protected static string $resource = DownloadableFoldersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
