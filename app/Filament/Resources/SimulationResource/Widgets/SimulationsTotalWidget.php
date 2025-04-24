<?php

namespace App\Filament\Resources\SimulationResource\Widgets;

use App\Models\Simulation;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SimulationsTotalWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = User::select(['simulations_count', 'name'])->whereType('partner')
            ->orderBy('simulations_count', 'desc')->first();

        $uniqueTotal = Simulation::select(\DB::raw('COUNT(DISTINCT document) as total'))->first()->total ?? 0;

        return [
            Stat::make('Total de Simulações', Simulation::count()),
            Stat::make('Total de Clientes Unicos', $uniqueTotal),
            Stat::make('Parceiro com mais Simulações', $user->simulations_count > 0 ? $user->name : '-'),
        ];
    }
}
