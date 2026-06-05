<?php

namespace App\Filament\Resources\DepartmentMembers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepartmentMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('position')
                    ->maxLength(255),
                FileUpload::make('image_url')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions(['9:16'])
                    ->disk('public')
                    ->visibility('public')
                    ->directory('images')
                    ->columnSpanFull(),

                TextInput::make('order')

                    ->numeric()
                    ->default(0),
            ]);
    }
}
