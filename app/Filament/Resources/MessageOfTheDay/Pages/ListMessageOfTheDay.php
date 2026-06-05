<?php

namespace App\Filament\Resources\MessageOfTheDay\Pages;

use App\Filament\Resources\MessageOfTheDay\MessageOfTheDayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMessageOfTheDay extends ListRecords
{
    protected static string $resource = MessageOfTheDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
