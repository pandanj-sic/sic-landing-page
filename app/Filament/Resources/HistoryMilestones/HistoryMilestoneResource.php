<?php

namespace App\Filament\Resources\HistoryMilestones;

use App\Filament\Resources\HistoryMilestones\Pages\CreateHistoryMilestone;
use App\Filament\Resources\HistoryMilestones\Pages\EditHistoryMilestone;
use App\Filament\Resources\HistoryMilestones\Pages\ListHistoryMilestones;
use App\Filament\Resources\HistoryMilestones\Schemas\HistoryMilestoneForm;
use App\Filament\Resources\HistoryMilestones\Tables\HistoryMilestonesTable;
use App\Models\HistoryMilestone;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HistoryMilestoneResource extends Resource
{
    protected static ?string $model = HistoryMilestone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'History';

    protected static ?string $modelLabel = 'Milestone';

    protected static ?string $pluralModelLabel = 'Milestones';

    public static function form(Schema $schema): Schema
    {
        return HistoryMilestoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoryMilestonesTable::configure($table);
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
            'index' => ListHistoryMilestones::route('/'),
            'create' => CreateHistoryMilestone::route('/create'),
            'edit' => EditHistoryMilestone::route('/{record}/edit'),
        ];
    }
}
