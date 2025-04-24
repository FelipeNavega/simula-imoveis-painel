<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\SimulationResource\Pages;
use App\Models\Simulation;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\Forms\ConsortiumForm;
use App\Filament\Pages\Forms\CreditPropertyGuaranteeForm;
use App\Filament\Pages\Forms\RealEstateCreditForm;
use App\Filament\Resources\SimulationResource as AdminSimulationResource;
use Filament\Facades\Filament;

class SimulationResource extends Resource
{
    protected static ?string $model = Simulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Todas as Simulações';

    protected static ?string $pluralLabel = 'Simulações';

    protected static ?string $label = 'Simulação';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->words(2)
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('document_with_mask')
                    ->label('forms.document')->translateLabel()
                    ->searchable(query: fn($query, $search) => $query->searchNumericValues('document', $search))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('municipios_estados_id')
                    ->label('Cidade')
                    ->getStateUsing(fn($record) => $record->municipiosEstados?->municipio_estado)
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('form_type')
                    ->label('forms.form_type')->translateLabel(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('general.created_at')->translateLabel()
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->filters(AdminSimulationResource::globalFilters())
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label(trans('general.created_at'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('created_by')
                    ->label(__('general.created_by'))
                    ->getTitleFromRecordUsing(fn($record) => $record->partner->name ?? 'Nenhum')
                    ->collapsible(),

                Tables\Grouping\Group::make('municipios_estados_id')
                    ->label('Estado/Cidade')
                    ->getTitleFromRecordUsing(fn($record) => $record->municipiosEstados->municipio_estado)
                    ->collapsible(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('created_by', Filament::auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSimulations::route('/'),
            'real_estate_credit' => RealEstateCreditForm::route('/real_estate_credit'),
            'credit_property_guarantee' => CreditPropertyGuaranteeForm::route('/credit_property_guarantee'),
            'consortium' => ConsortiumForm::route('/consortium'),
            'search-document' => Pages\SearchDocument::route('/search-document'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return AdminSimulationResource::infolist($infolist);
    }
}
