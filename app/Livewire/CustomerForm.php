<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\RawJs;
use Livewire\Component;
use Illuminate\Contracts\View\View;

/**
 * @property Form $form
 */
class CustomerForm extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithFormActions;

    public ?array $data = [
        'name'     => '',
        'phone'    => '',
        'email'    => '',
        'document' => '',
    ];

    public bool $acceptTerms = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('forms.name')->translateLabel()
                            ->required()
                            ->extraInputAttributes(['id' => 'input-name']),
                        TextInput::make('phone')
                            ->label('forms.phone')->translateLabel()
                            ->required()
                            ->mask(RawJs::make(<<<'JS'
$input.replaceAll(/\D/g, '').length <= 10 ? '(99) 9999-9999' : '(99) 99999-9999'
JS
                            ))
                            ->extraInputAttributes(['id' => 'input-phone']),
                        TextInput::make('email')
                            ->label('forms.email')->translateLabel()
                            ->email()
                            ->required()
                            ->extraInputAttributes(['id' => 'input-email']),
                        TextInput::make('document')
                            ->label('forms.document')->translateLabel()
                            ->required()
                            ->rules([
                                function () {
                                    return static function (string $attribute, $value, \Closure $fail) {
                                        $value = preg_replace('/\D/', '', $value);
                                        if (strlen($value) !== 11 && strlen($value) !== 14) {
                                            $fail('O documento é invalido');
                                        }
                                    };
                                },
                            ])
                            ->mask(RawJs::make(<<<'JS'
$input.replaceAll(/\D/g, '').length <= 11 ? '999.999.999-99' : '99.999.999/9999-99'
JS
                            ))
                            ->extraInputAttributes(['id' => 'input-document']),
                    ]),
            ])
            ->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make('search')
                ->label('Consultar')
                ->action('submit'),
        ];
    }

    public function submit(): void
    {
        $this->form->getState();
        \Log::info('Dados do formulário: ', $this->data);
        $this->dispatch('pdfDataUpdated', $this->data);
        $this->dispatch('showForm', $this->data);
    }

    // Método opcional para padronizar a extração de dados para PDF.
    public function getPdfData(): array
    {
        return $this->data;
    }

    public function render(): View
    {
        return view('livewire.customer-form');
    }
}
