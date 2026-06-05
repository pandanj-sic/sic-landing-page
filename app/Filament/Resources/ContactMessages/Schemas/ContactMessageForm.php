<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->disabled()
                    ->maxLength(255),

                TextInput::make('email')
                    ->disabled()
                    ->maxLength(255),

                TextInput::make('subject')
                    ->disabled()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('message')
                    ->disabled()
                    ->columnSpanFull()
                    ->rows(6),

                Toggle::make('is_read')
                    ->label('Mark as Read')
                    ->default(false),
            ]);
    }
}
