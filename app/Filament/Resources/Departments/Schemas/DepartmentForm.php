<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignorable: fn ($record) => $record)
                    ->maxLength(255),

                Textarea::make('description')
                    ->columnSpanFull(),

                FileUpload::make('image_url')
                    ->image()
                    ->imageEditor()
                    ->avatar()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('images'),

                RichEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsDirectory('departments-attachments')
                    ->columnSpanFull()
                    ->nullable(),

                Section::make('Department Carousels')
                    ->description('Manage carousel slides specific to this department.')
                    ->schema([
                        Repeater::make('carousels')
                            ->relationship('carousels')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->required()
                                    ->rows(2),
                                FileUpload::make('image_url')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatioOptions(['16:9'])
                                    ->required()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('images'),
                                TextInput::make('button_text')
                                    ->maxLength(255),
                                TextInput::make('button_url')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('order')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->orderColumn('order')
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->grid(2)
                            ->collapsible()
                            ->cloneable(),
                    ])
                    ->collapsible(),
            ]);
    }
}
