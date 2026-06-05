<?php

namespace App\Filament\Resources\MessageOfTheDay\Pages;

use App\Filament\Resources\MessageOfTheDay\MessageOfTheDayResource;
use App\Models\MessageOfTheDay;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMessageOfTheDay extends EditRecord
{
    protected static string $resource = MessageOfTheDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->is_active) {
            MessageOfTheDay::query()
                ->where('id', '!=', $record->id)
                ->update(['is_active' => false]);
        }
    }
}
