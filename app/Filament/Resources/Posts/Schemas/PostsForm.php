<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Content')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->fileAttachmentsDirectory('posts-attachments')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Media')
                    ->schema([
                        FileUpload::make('image_url')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9'])
                            ->columnSpanFull()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('images'),

                    ]),
                Section::make('Settings')
                    ->schema([
                        Radio::make('type')
                            ->options(PostType::class)
                            ->required()
                            ->default(PostType::Article)
                            ->live()
                            ->columnSpanFull(),
                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->default(false),
                    ])
                    ->columns(2),
                Section::make('Event Details')
                    ->schema([
                        DatePicker::make('start_date')
                            ->required()
                            ->native(false),
                        DatePicker::make('end_date')
                            ->native(false)
                            ->afterOrEqual('start_date'),
                        TextInput::make('location')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn ($get): bool => $get('type') === PostType::Event->value || $get('type') === PostType::Event),
                Section::make('Metadata')
                    ->schema([
                        KeyValue::make('metadata')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }
}
