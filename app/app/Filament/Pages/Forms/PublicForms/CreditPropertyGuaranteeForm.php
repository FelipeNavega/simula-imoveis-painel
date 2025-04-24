<?php

namespace App\Filament\Pages\Forms\PublicForms;

use App\Actions\SaveSimulation;
use App\Enums\FormTypeEnum;
use App\Filament\Pages\Forms\BaseCreditPropertyGuaranteeForm;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\MaxWidth;

class CreditPropertyGuaranteeForm extends SimplePage
{
    use InteractsWithFormActions;
    use BaseCreditPropertyGuaranteeForm;

    protected static string $view = 'filament.pages.simulation-form';

    public function __construct()
    {
        $this->setPartnerCode();
        $this->setPublic();
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::FiveExtraLarge;
    }

    public function getFormActions(): array
    {
        return $this->formActions();
    }
}
