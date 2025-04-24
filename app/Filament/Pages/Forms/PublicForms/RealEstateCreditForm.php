<?php

namespace App\Filament\Pages\Forms\PublicForms;

use App\Actions\SaveSimulation;
use App\Enums\FormTypeEnum;
use App\Filament\Pages\Forms\BaseRealEstateCreditForm;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class RealEstateCreditForm extends SimplePage
{
    use InteractsWithFormActions;
    use BaseRealEstateCreditForm;

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
