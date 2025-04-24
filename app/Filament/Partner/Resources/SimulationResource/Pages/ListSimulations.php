<?php

namespace App\Filament\Partner\Resources\SimulationResource\Pages;

use App\Filament\Partner\Resources\SimulationResource;
use App\Filament\Partner\Resources\SimulationResource\Widgets\SimulationsTotalWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSimulations extends ListRecords
{
    protected static string $resource = SimulationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            SimulationsTotalWidget::class,
        ];
    }
}
