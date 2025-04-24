<?php

namespace App\Filament\Pages\Forms;

use App\Actions\SaveSimulation;
use App\Filament\Resources\SimulationResource;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Webbingbrasil\FilamentCopyActions\Pages\Actions\CopyAction;

/**
 * @property Form $form
 */
class ConsortiumForm extends Page
{
    use InteractsWithFormActions;
    use BaseConsortiumForm;

    protected static string $resource = SimulationResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.simulation-form-admin';

    public function generateResults(): void
    {
        $inputs = $this->form->getState();
        $data = $this->getData();

        SaveSimulation::run($data);
        Notification::make()
            ->title('Simulação concluída com sucesso!')
            ->success()
            ->send();

        $this->calculoResults = $this->calculateValues($inputs);
    }

    public function hasLogo(): bool
    {
        return true;
    }

    public function getFormActions(): array
    {
        return $this->formActions();
    }

    protected function getHeaderActions(): array
    {
        $route = route('partner.simulations.consortium', [
            'partner_code' => Filament::auth()->user()->partner_code,
        ]);

        return [
            Action::make('public-link')
                ->label('Formulário Publico')
                ->url($route, true)
                ->icon('heroicon-c-link'),
            CopyAction::make('public-copyable')
                ->copyable($route)
                ->label('Copiar Link')
                ->successNotificationTitle('Link copiado com sucesso!')
                ->link()
        ];
    }
}
