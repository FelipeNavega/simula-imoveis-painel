<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PropertyStateEnum: string implements HasLabel
{
    use BaseEnum;

    case NEW = 'new';
    case USED = 'used';

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'Novo',
            self::USED => 'Usado',
        };
    }
}
