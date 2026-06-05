<?php

namespace App\Filament\Resources\MessageOfTheDay\Schemas;

use App\Models\MessageOfTheDay;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MessageOfTheDayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Person')
                    ->schema([
                        TextInput::make('person_name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('person_image')
                            ->label('Photo')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('message-of-the-day')
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Message')
                    ->schema([
                        Textarea::make('message')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Section::make('Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Only one message can be active at a time. Activating this will deactivate all others.')
                            ->afterStateUpdated(function (bool $state, ?MessageOfTheDay $record) {
                                if ($state) {
                                    MessageOfTheDay::query()
                                        ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                        ->update(['is_active' => false]);
                                }
                            })
                            ->live(),
                    ]),
            ]);
    }
}
