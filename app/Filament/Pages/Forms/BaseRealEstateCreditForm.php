<?php

namespace App\Filament\Pages\Forms;

use App\Enums\AmortizationSystemEnum;
use App\Enums\FormTypeEnum;
use App\Enums\PropertyStateEnum;
use App\Enums\PropertyTypeEnum;
use App\Models\MunicipiosEstados;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Number;

/**
 * @property Form $form
 */
trait BaseRealEstateCreditForm
{
    use BaseFormTrait;

    public FormTypeEnum $formType = FormTypeEnum::REAL_ESTATE_CREDIT;

    protected function getFormSchema(): array
    {
        return [
            Radio::make('property_type')
                ->columns()
                ->label('forms.questions.property_type')->translateLabel()
                ->options(PropertyTypeEnum::class)
                ->required(),

            Grid::make()
                ->columns()
                ->schema([
                    DatePicker::make('date_of_birth')
                        ->label('forms.questions.date_of_birth')->translateLabel()
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->prefixIcon('heroicon-o-calendar-days')
                        ->rules([
                            fn () => $this->checkBirthdayValid()
                        ])
                        ->required(),

                    TextInput::make('property_value')
                        ->label('forms.questions.property_value')->translateLabel()
                        ->extraAlpineAttributes(['oninput' => 'currencyMask(this)'])
                        ->prefix('R$')
                        ->required(),
                ]),

            Grid::make('location')
                ->columns()
                ->schema([
                    Select::make('state_uf')
                        ->label('forms.localization.state')->translateLabel()
                        ->searchable()->preload()
                        ->optionsLimit(26)
                        ->options(
                            MunicipiosEstados::select(['estado', 'uf'])
                                ->orderBy('estado')
                                ->pluck('estado', 'uf')
                        )
                        ->required(),

                    Select::make('city_id')
                        ->label('forms.localization.city')->translateLabel()
                        ->searchable()
                        ->optionsLimit(9999)
                        ->options(fn(Get $get) => $this->getCities($get))
                        ->required(),
                ]),

            Radio::make('property_state')->columns()
                ->label('forms.questions.property_state')->translateLabel()
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
                ->label('forms.questions.entry_value')->translateLabel()
                ->extraAlpineAttributes(['oninput' => 'currencyMask(this)'])
                ->prefix('R$ ')
                ->required(),

            TextInput::make('installments')
                ->label('forms.questions.installments_form')->translateLabel()
                ->prefix('Meses')
                ->mask('999')
                ->rules([
                    fn (Get $get) => $this->checkBirthdayRange($get),
                    fn (Get $get) => $this->checkMaxInstallmentValue($get),
                ])
                ->required(),

            Radio::make('amortization_system')
                ->columns()
                ->label('forms.questions.amortization_system')->translateLabel()
                ->options(AmortizationSystemEnum::class)
                ->required(),
        ];
    }

    protected function getData(): array
    {
        $inputs = $this->form->getState();

        return [
            'customer' => $this->customer,
            'form_type' => $this->formType,
            'created_by' => $this->getCreatedBy(),
            'city_id' => $inputs['city_id'],
            'answers' => [
                'property_type' => $inputs['property_type'],
                'property_value' => (float) str_replace(['.', ','], ['', '.'], $inputs['property_value']),
                'property_state' => $inputs['property_state'],
                'live_work_in_city' => $inputs['live_work_in_city'],
                'property_or_loan_in_city' => $inputs['property_or_loan_in_city'],
                'has_fgts' => $inputs['has_fgts'],
                'has_fgts_with_percent_of_value' => $inputs['has_fgts_with_percent_of_value'],
                'entry_value' => (float) str_replace(['.', ','], ['', '.'], $inputs['entry_value']),
                'installments' => $inputs['installments'],
                'amortization_system' => $inputs['amortization_system'],
                'date_of_birth' => $inputs['date_of_birth'],
            ]
        ];
    }

    private function calculateValues(array $values): array
    {
        [$banks, $bankExtras] = \App\Models\Form::where('type', $this->formType)->first()->bankData();
        $this->banks = $banks;
        $this->bankExtras = $bankExtras;

        $result = collect();
        $propertyValue = (float) str_replace(['.', ','], ['', '.'], $values['property_value']);
        $entryValue = (float) str_replace(['.', ','], ['', '.'], $values['entry_value']);

        $maxLoanAmount = $propertyValue * (60 / 100);

        foreach ($banks as $index => $bank) {
            $extras = $bankExtras[$index];
            $jurosEfetiva = (float) $extras['jurosEfetiva'];

            $prestacoes = [];
            $capitalFinanciado = $propertyValue - $entryValue;
            $jurosMensal = $jurosEfetiva / (12 * 100);

//            if(isset($extras['ipca']) && $extras['ipca']) {
//                $jurosMensal *= $this->ipcaValue();
//            }

            // Fields
            $installments = (int) $values['installments'];

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

            $result->put($index, ['installments' => $prestacoes, 'max_loan_value' => $maxLoanAmount]);
        }

        return $result->toArray();
    }
}
