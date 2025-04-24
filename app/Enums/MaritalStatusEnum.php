<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MaritalStatusEnum: string implements HasLabel
{
    use BaseEnum;

    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case SEPARATED = 'separated';
    case WIDOWED = 'widowed';

    public function getLabel(): string
    {
        return match ($this) {
            self::SINGLE => 'Solteiro(a)',
            self::MARRIED => 'Casado(a)',
            self::DIVORCED => 'Divorciado(a)',
            self::SEPARATED => 'Separado(a)',
            self::WIDOWED => 'Viuvo(a)',
        };
    }
}
