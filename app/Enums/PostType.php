<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PostType: string implements HasLabel
{
    case Article = 'article';
    case Announcement = 'announcement';
    case Event = 'event';

    public function getLabel(): string
    {
        return match ($this) {
            self::Article => 'Article',
            self::Announcement => 'Announcement',
            self::Event => 'Event',
        };
    }
}
