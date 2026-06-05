<?php

namespace App\Filament\Resources\HistoryMilestones\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HistoryMilestoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('year')
                    ->required()
                    ->maxLength(255),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),

                Select::make('icon')
                    ->options([
                        'book-open' => 'Book Open',
                        'eye' => 'Eye',
                        'graduation-cap' => 'Graduation Cap',
                        'hand-heart' => 'Hand Heart',
                        'handshake' => 'Handshake',
                        'heart' => 'Heart',
                        'landmark' => 'Landmark',
                        'lightbulb' => 'Lightbulb',
                        'medal' => 'Medal',
                        'school' => 'School',
                        'sparkles' => 'Sparkles',
                        'star' => 'Star',
                        'target' => 'Target',
                        'trophy' => 'Trophy',
                        'users' => 'Users',
                    ])
                    ->required(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
