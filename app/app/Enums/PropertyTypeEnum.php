<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PropertyTypeEnum: string implements HasLabel
{
    use BaseEnum;

    case RESIDENTIAL = 'residential';
    case COMMERCIAL = 'commercial';

    public function getLabel(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'Residencial',
            self::COMMERCIAL => 'Comercial',
        };
    }
}
