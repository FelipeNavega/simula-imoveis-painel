<?php

namespace App\Filament\Resources\FormResource\Pages;

use App\Filament\Resources\FormResource;
use App\Models\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cache-clear')
                ->label('Limpar cache')
                ->icon('heroicon-o-trash')
                ->action(function (Form $record) {
                    Cache::forget('form-' . $record->getKey());

                    Notification::make()
                        ->title('Cache limpo com sucesso!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
