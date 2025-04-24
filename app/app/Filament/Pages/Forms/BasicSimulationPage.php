<?php

namespace App\Filament\Pages\Forms;

use App\Actions\SaveSimulation;
use App\Enums\AmortizationSystemEnum;
use App\Enums\FormTypeEnum;
use App\Enums\PropertyStateEnum;
use App\Enums\PropertyTypeEnum;
use App\Models\MunicipiosEstados;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Livewire\Attributes\On;

/**
 * @property Form $form
 */
trait BasicSimulationPage
{
    /**
     * @var array<string, mixed> | null
     */
    public array $data = [
        'state_uf' => '',
        'city_id' => ''
    ];

    public array $customer = [];

    protected array $banks = [
        'itau-personalite' => [
            'name' => 'Itaú Personalité',
            'logo' => 'https://www.itau.com.br/media/dam/m/4a07ae202ec015a3/original/Person_icone_92x92px.png'
        ],
        'itau-uniclass' => [
            'name' => 'Itaú Uniclass',
            'logo' => 'https://www.itau.com.br/media/dam/m/1ecdbc238acac9ae/original/itau-uniclass-logo.png'
        ],
        'itau-agencia' => [
            'name' => 'Itaú Agência',
            'logo' => 'https://www.consultoriac3.com.br/wp-content/uploads/2024/04/Itau.png'
        ],
        'bradesco-exclusive' => [
            'name' => 'Bradesco Exclusive',
            'logo' => 'https://banco.bradesco/static/wcomp/header-footer/assets/classic/img/icon-app-bia.png'
        ],
        'bradesco-prime' => [
            'name' => 'Bradesco Prime',
            'logo' => 'https://banco.bradesco/static/wcomp/header-footer/assets/classic/img/icon-app-bia.png'
        ],
        'bradesco-agencia' => [
            'name' => 'Bradesco Agência',
            'logo' => 'https://banco.bradesco/static/wcomp/header-footer/assets/classic/img/icon-app-bia.png'
        ],
        'santander-bonificada' => [
            'name' => 'Santander Bonificada',
            'logo' => 'https://thewestfieldnews.com/wp-content/uploads/2014/12/image46.jpg'
        ],
        'santander-sem-relacionamento' => [
            'name' => 'Santander Sem Relacionamento',
            'logo' => 'https://thewestfieldnews.com/wp-content/uploads/2014/12/image46.jpg'
        ],
        'inter-unica' => [
            'name' => 'Inter Única',
            'logo' => 'data:image/png;base64,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'
        ],
        'caixa-com-relacionamento' => [
            'name' => 'Caixa Com Relacionamento',
            'logo' => 'https://logodownload.org/wp-content/uploads/2014/02/caixa-logo-0.png'
        ],
        'caixa-sem-relacionamento' => [
            'name' => 'Caixa Sem Relacionamento',
            'logo' => 'https://logodownload.org/wp-content/uploads/2014/02/caixa-logo-0.png'
        ]
    ];

    protected array $bankExtras = [
        'itau-personalite' => [
            'jurosEfetiva' => 10.49,
            'ltv' => 'Até 90%',
            'avaliacao' => 'R$1.950,00'
        ],
        'itau-uniclass' => [
            'jurosEfetiva' => 11.09,
            'ltv' => 'Até 90%',
            'avaliacao' => 'R$1.950,00'
        ],
        'itau-agencia' => [
            'jurosEfetiva' => 11.59,
            'ltv' => 'Até 90%',
            'avaliacao' => 'R$1.950,00'
        ],
        'bradesco-exclusive' => [
            'jurosEfetiva' => 10.99,
            'ltv' => 'Até 80%',
            'avaliacao' => 'R$2.114,00'
        ],
        'bradesco-prime' => [
            'jurosEfetiva' => 10.49,
            'ltv' => 'Até 80%',
            'avaliacao' => 'R$2.114,00'
        ],
        'bradesco-agencia' => [
            'jurosEfetiva' => 11.49,
            'ltv' => 'Até 80%',
            'avaliacao' => 'R$2.114,00'
        ],
        'santander-bonificada' => [
            'jurosEfetiva' => 10.49,
            'ltv' => 'Até 80%',
            'avaliacao' => 'R$1.850,00'
        ],
        'santander-sem-relacionamento' => [
            'jurosEfetiva' => 11.49,
            'ltv' => 'Até 80%',
            'avaliacao' => 'R$1.850,00'
        ],
        'inter-unica' => [
            'jurosEfetiva' => 10.90,
            'ltv' => 'Até 80%',
            'avaliacao' => 'R$954,00'
        ],
        'caixa-com-relacionamento' => [
            'jurosEfetiva' => 9.89,
            'ltv' => 'Até 80%',
            'avaliacao' => 'R$950,00 até R$3.100,00'
        ],
        'caixa-sem-relacionamento' => [
            'jurosEfetiva' => 9.99,
            'ltv' => 'Até 80%',
            'avaliacao' => 'R$950,00 até R$3.100,00'
        ]
    ];

    public bool $showForm = false;

    public array $calculoResults = [];

    public function formActions(): array
    {
        return [
            Action::make('simular')
                ->label('Simular Financiamento')
                ->action('generateResults')
        ];
    }

    protected function formStatePath(): ?string
    {
        return 'data';
    }

    protected function formSchema(): array
    {
        return [
            Radio::make('property_type')
                ->columns()
                ->label('forms.property_type')->translateLabel()
                ->options(PropertyTypeEnum::class)
                ->required(),

            TextInput::make('property_value')
                ->label('forms.property_value')->translateLabel()
                ->extraAlpineAttributes(['oninput' => 'currencyMask(this)'])
                ->prefix('R$')
                ->maxLength(10)
                ->required(),

            Grid::make('location')
                ->columns()
                ->schema([
                    Select::make('state_uf')
                        ->label('forms.localization.state')->translateLabel()
                        ->searchable()->preload()
                        ->options(
                            MunicipiosEstados::select(['estado', 'uf'])
                                ->orderBy('estado')
                                ->pluck('estado', 'uf')
                        )
                        ->required(),

                    Select::make('city_id')
                        ->label('forms.localization.city')->translateLabel()
                        ->searchable()
                        ->options(fn(Get $get) => $this->getCities($get))
                        ->required(),
                ]),

            Radio::make('property_state')->columns()
                ->label('forms.property_state')->translateLabel()
                ->options(PropertyStateEnum::class)
                ->required(),

            Radio::make('live_work_in_city')->columns()
                ->label('forms.questions.live_work_in_city')->translateLabel()
                ->boolean()
                ->required(),

            Radio::make('property_or_loan_in_city')->columns()
                ->label('forms.questions.property_or_loan_in_city')->translateLabel()
                ->boolean()
                ->required(),

            Radio::make('has_fgts')->columns()
                ->label('forms.questions.has_fgts')->translateLabel()
                ->boolean()->required(),

            Radio::make('has_fgts_with_percent_of_value')->columns()
                ->label('forms.questions.has_fgts_with_percent_of_value')->translateLabel()
                ->boolean()->required(),

            TextInput::make('entry_value')
                ->label('forms.entry_value')->translateLabel()
                ->extraAlpineAttributes(['oninput' => 'currencyMask(this)'])
                ->prefix('R$ ')
                ->maxLength(10)
                ->required(),

            TextInput::make('installments')
                ->label('forms.installments_form')->translateLabel()
                ->prefix('Meses')
                ->mask('999')
                ->required(),

            Radio::make('amortization_system')
                ->columns()
                ->label('forms.amortization_system')->translateLabel()
                ->options(AmortizationSystemEnum::class)
                ->required(),
        ];
    }

    public function generateResults(): void
    {
        $data = $this->form->getState();
        $data['customer'] = $this->customer;
        $data['form_type'] = $this->getFormType();
        $data['created_by'] = $this->getCreatedBy();
        SaveSimulation::run($data);
        $this->calculoResults = $this->calculateValues($data);
    }

    public function getCreatedBy(): ?int
    {
        return null;
    }

    public function getFormType(): FormTypeEnum
    {
        return FormTypeEnum::CREDIT_PROPERTY_GUARANTEE;
    }

    private function calculateValues(array $values): array
    {
        $result = collect();
        $propertyValue = (int)str_replace(',', '', $values['property_value']);
        $entryValue = (int)str_replace(',', '', $values['entry_value']);

        foreach ($this->banks as $index => $bank) {
            $extras = $this->bankExtras[$index];
            $jurosEfetiva = $extras['jurosEfetiva'];

            $prestacoes = [];
            $capitalFinanciado = $propertyValue - $entryValue;
            $jurosMensal = $jurosEfetiva / (12 * 100);

            // Fields
            $installments = (int)$values['installments'];

            for ($j = 0; $j < 3; $j++) {
                $y = null;

                if ($j === 0) {
                    $y = 0;// Primeira parcela paga no financiamento ($installments total)
                } else if ($j === 1) {
                    $y = ($installments / 2) - 1;
                    // Parcela paga na metade do financiamento
                } else if ($j === 2) {
                    $y = $installments - 1;
                    // Última parcela do financiamento
                }

                if ($values['amortization_system'] === "sac") {
                    // Equação para SAC
                    $amortizacaoSac = $capitalFinanciado / $installments;
                    $saldoDevedorSac = $capitalFinanciado - ($amortizacaoSac * $y);
                    $montanteJurosSac = $jurosMensal * $saldoDevedorSac;
                    $valorFinal = $amortizacaoSac + $montanteJurosSac;
                    $prestacoes[] = Number::currency($valorFinal, 'BRL');
                    continue;
                }

                // Equação para PRICE
                $potencia_1 = ((1 + $jurosMensal) ** $installments) * $jurosMensal;
                $potencia_2 = ((1 + $jurosMensal) ** $installments) - 1;
                $valorFinal = $capitalFinanciado * ($potencia_1 / $potencia_2);
                $prestacoes[] = Number::currency($valorFinal, 'BRL');
            }

            $result->put($index, ['installments' => $prestacoes]);
        }

        return $result->toArray();
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

    #[On('showForm')]
    public function showForm(array $data): void
    {
        $this->showForm = !$this->showForm;
        $this->customer = $data;
    }
}
