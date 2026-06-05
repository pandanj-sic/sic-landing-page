<?php

namespace App\Filament\Resources\DownloadableFolders;

use App\Filament\Resources\DownloadableFolders\Pages\CreateDownloadableFolders;
use App\Filament\Resources\DownloadableFolders\Pages\EditDownloadableFolders;
use App\Filament\Resources\DownloadableFolders\Pages\ListDownloadableFolders;
use App\Filament\Resources\DownloadableFolders\Schemas\DownloadableFoldersForm;
use App\Filament\Resources\DownloadableFolders\Tables\DownloadableFoldersTable;
use App\Models\DownloadableFolder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DownloadableFoldersResource extends Resource
{
    protected static ?string $model = DownloadableFolder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Downloadables';

    protected static ?string $modelLabel = 'Folder';

    protected static ?string $pluralModelLabel = 'Folders';

    public static function form(Schema $schema): Schema
    {
        return DownloadableFoldersForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadableFoldersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDownloadableFolders::route('/'),
            'create' => CreateDownloadableFolders::route('/create'),
            'edit' => EditDownloadableFolders::route('/{record}/edit'),
        ];
    }
}
