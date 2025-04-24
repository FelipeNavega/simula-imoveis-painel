<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AmortizationSystemEnum: string implements HasLabel
{
    use BaseEnum;

    case SAC = 'sac';
    case PRICE = 'price';

    public function getLabel(): string
    {
        return match ($this) {
            self::SAC => 'SAC',
            self::PRICE => 'PRICE',
        };
    }
}
