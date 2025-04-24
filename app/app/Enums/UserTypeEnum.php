<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserTypeEnum: string implements HasLabel
{
    use BaseEnum;

    case ADMIN = 'admin';
    case PARTNER = 'partner';

    public function getLabel(): string
    {
        return match ($this) {
            self::PARTNER => 'Parceiro',
            self::ADMIN => 'Administrador',
        };
    }
}
