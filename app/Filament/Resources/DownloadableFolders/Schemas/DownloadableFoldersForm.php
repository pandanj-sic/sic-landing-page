<?php

namespace App\Filament\Resources\DownloadableFolders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DownloadableFoldersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Folder Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('parent_id')
                            ->label('Parent Folder')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('None (root folder)'),
                        TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),
                Section::make('Files')
                    ->schema([
                        Repeater::make('files')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                FileUpload::make('file_path')
                                    ->label('File')
                                    ->required()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('downloadables')
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/vnd.ms-powerpoint',
                                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                        'image/jpeg',
                                        'image/png',
                                        'image/webp',
                                        'image/gif',
                                        'text/plain',
                                        'application/zip',
                                        'application/x-rar-compressed',
                                    ])
                                    ->maxSize(51200)
                                    ->columnSpanFull()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $file = is_array($state) ? reset($state) : $state;
                                            if ($file instanceof TemporaryUploadedFile) {
                                                $set('file_type', $file->getMimeType());
                                                $set('file_size', $file->getSize());
                                            }
                                        }
                                    })
                                    ->live(),
                                TextInput::make('file_type')
                                    ->label('File Type')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('file_size')
                                    ->label('File Size (bytes)')
                                    ->disabled()
                                    ->dehydrated(),
                                Textarea::make('description')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                TextInput::make('order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ])
                            ->columns(2)
                            ->orderColumn('order')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
