<?php

namespace App\Filament\Partner\Resources\SimulationResource\Widgets;

use App\Models\Simulation;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SimulationsTotalWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Filament::auth()->id();

        $uniqueTotal = Simulation::select(\DB::raw('COUNT(DISTINCT document) as total'))
            ->createdBy($userId)->first()->total ?? 0;

        return [
            Stat::make('Total de Simulações', Simulation::createdBy($userId)->count()),
            Stat::make('Total de Clientes Unicos', $uniqueTotal),
        ];
    }
}
