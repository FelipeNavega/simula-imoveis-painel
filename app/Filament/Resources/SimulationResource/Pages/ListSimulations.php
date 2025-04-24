<?php

namespace App\Filament\Resources\SimulationResource\Pages;

use App\Filament\Resources\SimulationResource;
use App\Filament\Resources\SimulationResource\Widgets\SimulationsTotalWidget;
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
