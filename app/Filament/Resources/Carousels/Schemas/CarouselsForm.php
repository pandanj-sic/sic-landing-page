<?php

namespace App\Filament\Resources\Carousels\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CarouselsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Carousel Content')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('image_url')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions(['16:9'])
                            ->required()
                            ->columnSpanFull()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('images'),
                    ]),
                Section::make('Button')
                    ->schema([
                        TextInput::make('button_text')
                            ->maxLength(255),
                        TextInput::make('button_url')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Settings')
                    ->schema([
                        TextInput::make('order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
            ]);
    }
}
