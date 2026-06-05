<?php

namespace App\Filament\Resources\MessageOfTheDay\Pages;

use App\Filament\Resources\MessageOfTheDay\MessageOfTheDayResource;
use App\Models\MessageOfTheDay;
use Filament\Resources\Pages\CreateRecord;

class CreateMessageOfTheDay extends CreateRecord
{
    protected static string $resource = MessageOfTheDayResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        if ($record->is_active) {
            MessageOfTheDay::query()
                ->where('id', '!=', $record->id)
                ->update(['is_active' => false]);
        }
    }
}
