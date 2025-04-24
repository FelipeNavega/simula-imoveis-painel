<?php

namespace App\Filament\Pages\Forms;

use App\Actions\SaveSimulation;
use App\Models\Form;
use App\Models\MunicipiosEstados;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

/**
 * @property-read array<string, mixed> $banks
 * @property-read array<string, mixed> $bankExtras
 * @property-read array<int, string> $hiddenBanks
 */
trait BaseFormTrait
{
    use BirthdayLimitValidation;

    /**
     * @var array<string, mixed> | null
     */
    public array $data = [
        'state_uf' => '',
        'city_id' => '',
        'date_of_birth' => '',
        'partner_code' => '',
        'is_public' => false
    ];

    public bool $showForm = false;

    public array $calculoResults = [];

    public array $customer = [];

    public function __construct()
    {
        $this->setPartnerCode();
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function getTitle(): string|Htmlable
    {
        return $this->formType->getLabel();
    }

    private function getCities(Get $get): array|Collection
    {
        if (blank($get('state_uf'))) {
            return [];
        }

        return MunicipiosEstados::select(['id', 'municipio'])
            ->where('uf', $get('state_uf'))
            ->orderBy('municipio')->pluck('municipio', 'id');
    }

    private function getCreatedBy(): int|null
    {
        if(!request()->routeIs('partner.simulations.*') && Filament::auth()->check()) {
            return Filament::auth()->id();
        }

        if(!blank($this->data['partner_code'])) {
            return User::select('id')
                ->where('partner_code', $this->data['partner_code'])
                ->first()?->getKey();
        }

        return null;
    }

    private function setPublic(): void
    {
        $this->data['is_public'] = true;
    }

    private function setPartnerCode(): void
    {
        if(request()->routeIs('partner.simulations.*')) {
            $this->data['partner_code'] = request()->route('partner_code');
        }
    }

    public function getHeading(): string | Htmlable
    {
        return $this->formType->getLabel();
    }

    public function generateResults(): void
    {
        $inputs = $this->form->getState();
        $data = $this->getData();

//        SaveSimulation::run($data);
        Notification::make()->title('Simulação concluída com sucesso!')
            ->success()
            ->send();

        $this->calculoResults = $this->calculateValues($inputs);
    }

    public function formActions(): array
    {
        return [
            Action::make('simular')
                ->label('Simular Financiamento')
                ->action('generateResults')
        ];
    }

    public function getBreadcrumbs(): array
    {
        /** @var Resource $resource */
        $resource = static::getResource();

        return [
            $resource::getUrl() => $resource::getBreadcrumb(),
            0 => 'Nova Simulação',
        ];
    }

    private function ipcaValue(): float
    {
        return 3.93;
    }

    private function maxLoanAmount(Get $get): \Closure
    {
        return static function (string $attribute, string $value, \Closure $fail) use ($get) {
            $propertyValue = (float) str_replace(['.', ','], ['', '.'], $get('property_value'));
            $entryValue = (float) str_replace(['.', ','], ['', '.'], $value);
            $maxLoanAmount = $propertyValue * .6;

            if ($maxLoanAmount < $entryValue) {
                $fail(trans('forms.max_loan_amount'));
            }
        };
    }

    public function getBanks(): array
    {
        return collect($this->banks)
            ->filter(fn (array $bank, string $key) => !in_array($key, $this->hiddenBanks ?? [], true))
            ->toArray();
    }

    public function getViewData(): array
    {
        return [
            'banks' => $this->banks ?? [],
            'bankExtras' => $this->bankExtras ?? []
        ];
    }

    #[On('showForm')]
    public function showForm(array $data): void
    {
        $this->showForm = !$this->showForm;
        $this->customer = $data;
    }
}
