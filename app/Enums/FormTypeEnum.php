<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FormTypeEnum: string implements HasLabel
{
    use BaseEnum;

    case REAL_ESTATE_CREDIT = 'real_estate_credit';
    case CREDIT_PROPERTY_GUARANTEE = 'credit_property_guarantee';
    case CONSORTIUM = 'consortium';

    public function getLabel(): string
    {
        return match ($this) {
            self::REAL_ESTATE_CREDIT => 'Crédito Imobiliario',
            self::CREDIT_PROPERTY_GUARANTEE => 'Crédito com garantia de imóvel',
            self::CONSORTIUM => 'Consórcio',
        };
    }
}
