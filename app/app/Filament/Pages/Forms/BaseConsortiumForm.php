<?php

namespace App\Filament\Pages\Forms;

use App\Actions\SaveSimulation;
use App\Enums\FormTypeEnum;
use App\Models\MunicipiosEstados;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Number;

/**
 * @property Form $form
 */
trait BaseConsortiumForm
{
    use BaseFormTrait;

    public FormTypeEnum $formType = FormTypeEnum::CONSORTIUM;

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('consortium_value')
                        ->label('forms.questions.consortium_value')->translateLabel()
                        ->extraAlpineAttributes(['oninput' => 'currencyMask(this)'])
                        ->prefix('R$')
                        ->required(),
                    TextInput::make('months_pay')
                        ->label('forms.questions.months_pay')->translateLabel()
                        ->prefix('Meses')
                        ->mask('999')
                        ->numeric()
                        ->maxValue(240)
                        ->rules([
                            fn (Get $get) => $this->checkBirthdayRange($get),
                            fn (Get $get) => $this->checkMaxInstallmentValue($get),
                        ])
                        ->required(),
                ]),

            DatePicker::make('date_of_birth')
                ->label('forms.questions.date_of_birth')->translateLabel()
                ->displayFormat('d/m/Y')
                ->native(false)
                ->prefixIcon('heroicon-o-calendar-days')
                ->rules([
                    fn () => $this->checkBirthdayValid()
                ])
                ->required(),

            Grid::make()
                ->columns()
                ->schema([
                    Select::make('state_uf')
                        ->label('forms.localization.state')->translateLabel()
                        ->searchable()->preload()
                        ->options(26)
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

            TextInput::make('monthly_income')
                ->label('forms.questions.monthly_income')->translateLabel()
                ->extraAlpineAttributes(['oninput' => 'currencyMask(this)'])
                ->prefix('R$')
                ->required()
                ->hint('Pode se usar Até 30% da renda'),
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
                'consortium_value' => str_replace(['.', ','], ['', '.'], $inputs['consortium_value']),
                'months_pay' => (int) $inputs['months_pay'],
                'date_of_birth' => $inputs['date_of_birth'],
                'monthly_income' => str_replace(['.', ','], ['', '.'], $inputs['monthly_income']),
            ],
        ];
    }

    private function calculateValues(array $values): array
    {
        [$banks, $bankExtras] = \App\Models\Form::where('type', $this->formType)->first()->bankData();
        $this->banks = $banks;
        $this->bankExtras = $bankExtras;

        $result = collect();
        $consortiumValue = (float) str_replace(['.', ','], ['', '.'], $values['consortium_value']);

        foreach ($banks as $index => $bank) {
            $extras = $bankExtras[$index];
            $taxAdmin = $extras['tax_admin'] / 100;
            $reserva = $extras['reserva'] / 100;

            $tax = $taxAdmin + $reserva;
            $taxValue = $consortiumValue * $tax;

            $finalValue = $consortiumValue + $taxValue;
            $monthlyValue = $finalValue / $values['months_pay'];

            $finalValue = Number::currency($finalValue, 'BRL');
            $monthlyValue = Number::currency($monthlyValue, 'BRL');

            $result->put($index, compact('monthlyValue', 'finalValue'));
        }

        return $result->toArray();
    }
}
