<?php

namespace App\Filament\Resources\MessageOfTheDay;

use App\Filament\Resources\MessageOfTheDay\Pages\CreateMessageOfTheDay;
use App\Filament\Resources\MessageOfTheDay\Pages\EditMessageOfTheDay;
use App\Filament\Resources\MessageOfTheDay\Pages\ListMessageOfTheDay;
use App\Filament\Resources\MessageOfTheDay\Schemas\MessageOfTheDayForm;
use App\Filament\Resources\MessageOfTheDay\Tables\MessageOfTheDayTable;
use App\Models\MessageOfTheDay as MessageOfTheDayModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MessageOfTheDayResource extends Resource
{
    protected static ?string $model = MessageOfTheDayModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $recordTitleAttribute = 'person_name';

    protected static ?string $navigationLabel = 'Message of the Day';

    protected static ?string $modelLabel = 'Message';

    protected static ?string $pluralModelLabel = 'Messages';

    public static function form(Schema $schema): Schema
    {
        return MessageOfTheDayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MessageOfTheDayTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMessageOfTheDay::route('/'),
            'create' => CreateMessageOfTheDay::route('/create'),
            'edit' => EditMessageOfTheDay::route('/{record}/edit'),
        ];
    }
}
