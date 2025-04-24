<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PartnerTypeEnum:string implements HasLabel
{
    use BaseEnum;

    case ADMCONDOMINIO = 'admCondominio';
    case ASSESSORIA = 'assessoria';
    case CONSTRUTORA = 'construtora';
    case CONSULTORIA = 'consultoria';
    case CORRETOR = 'corretor';
    case IMOBILIARIA = 'imobiliaria';
    case MARKETPLACE = 'marketplace';
    case ONLINE = 'online';
    case OUTROS = 'outros';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADMCONDOMINIO => 'Administrador de Condomínio',
            self::ASSESSORIA => 'Assessoria',
            self::CONSTRUTORA => 'Construtora',
            self::CONSULTORIA => 'Consultor PJ',
            self::CORRETOR => 'Corretor',
            self::IMOBILIARIA => 'Imobiliária',
            self::MARKETPLACE => 'Marketplace',
            self::ONLINE => 'Online',
            self::OUTROS => 'Outros',
        };
    }
}
