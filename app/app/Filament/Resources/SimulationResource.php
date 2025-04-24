<?php

namespace App\Filament\Resources;

use App\Enums\AmortizationSystemEnum;
use App\Enums\FormTypeEnum;
use App\Enums\PropertyStateEnum;
use App\Enums\PropertyTypeEnum;
use App\Enums\UserTypeEnum;
use App\Models\MunicipiosEstados;
use App\Models\User;
use App\Filament\Resources\SimulationResource\Infolist\{Consortium, CreditPropertyGuarantee, RealEstateCredit};
use App\Filament\Resources\SimulationResource\Pages;
use App\Models\Simulation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SimulationResource extends Resource
{
    protected static ?string $model = Simulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Simulações';

    protected static ?string $pluralLabel = 'Simulações';

    protected static ?string $label = 'Simulação';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_by')
                    ->label('forms.partner_name')->translateLabel()
                    ->words(2)
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->partner?->name),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
                TernaryFilter::make('created_by_ternary')
                    ->label('Simulações criadas por')
                    ->placeholder('Todas as Simulações')
                    ->trueLabel('Parceiros')
                    ->falseLabel('Formulário publico')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('created_by'),
                        false: fn(Builder $query) => $query->whereNull('created_by'),
                    ),
                SelectFilter::make('created_by')
                    ->label('general.created_by')->translateLabel()
                    ->searchable()
                    ->multiple()
                    ->options(User::where('type', UserTypeEnum::PARTNER)->pluck('name', 'id')),
                ...self::globalFilters()
            ])
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

    public static function globalFilters(): array
    {
        return [
            SelectFilter::make('form_type')
                ->label('Tipo de Formulário')
                ->options(FormTypeEnum::class),
            SelectFilter::make('property_type')
                ->query(static fn(Builder $query, array $data): Builder => self::filterQuestions($query, $data, 'property_type'))
                ->label('Tipo de Imóvel')
                ->options(PropertyTypeEnum::class),
            SelectFilter::make('property_state')
                ->query(fn(Builder $query, array $data): Builder => self::filterQuestions($query, $data, 'property_state'))
                ->label('Condições do Imovel')
                ->options(PropertyStateEnum::class),
            SelectFilter::make('amortization_system')
                ->query(fn(Builder $query, array $data): Builder => self::filterQuestions($query, $data, 'amortization_system'))
                ->label('Amortização')
                ->options(AmortizationSystemEnum::class),
            Filter::make('location')
                ->form([
                    Select::make('state_uf')
                        ->label('forms.localization.state')->translateLabel()
                        ->options(MunicipiosEstados::select(['estado', 'uf'])
                            ->orderBy('estado')->pluck('estado', 'uf'))
                        ->searchable(),
                    Select::make('city')
                        ->label('forms.localization.city')->translateLabel()
                        ->options(fn(Get $get) => self::getCities($get))
                        ->searchable()
                ])->query(function (Builder $query, array $data): Builder {
                    if (filled($data['state_uf']) && filled($data['city'])) {
                        $query->where('municipios_estados_id', $data['city']);
                    }

                    if (filled($data['state_uf']) && blank($data['city'])) {
                        $query->byState($data['state_uf']);
                    }

                    return $query;
                }),
            Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from')
                        ->label('general.created_at')->translateLabel(),
                    DatePicker::make('created_until')
                        ->label('Criado Até'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
        ];
    }

    public static function filterQuestions(Builder $query, array $data, string $key): Builder
    {
        return $query->when($data['value'],
            fn(Builder $query, $value): Builder => $query->hasAnswer($key, $value)
        );
    }

    public static function getCities(Get $get): array|Collection
    {
        if (blank($get('state_uf'))) {
            return [];
        }

        return MunicipiosEstados::select(['id', 'municipio'])
            ->where('uf', $get('state_uf'))
            ->orderBy('municipio')->pluck('municipio', 'id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSimulations::route('/'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return match ($infolist->record->form_type) {
            FormTypeEnum::REAL_ESTATE_CREDIT => RealEstateCredit::infolist($infolist),
            FormTypeEnum::CREDIT_PROPERTY_GUARANTEE => CreditPropertyGuarantee::infolist($infolist),
            FormTypeEnum::CONSORTIUM => Consortium::infolist($infolist),
        };
    }

}
