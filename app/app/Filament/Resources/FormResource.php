<?php

namespace App\Filament\Resources;

use App\Enums\FormTypeEnum;
use App\Filament\Resources\FormResource\Pages;
use App\Models\Bank;
use App\Models\Form as FormModel;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormResource extends Resource
{
    protected static ?string $model = FormModel::class;

    protected static ?string $slug = 'forms';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Formulário';

    protected static ?string $pluralLabel = 'Formulários';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Grid::make()->schema([
                    Select::make('type')
                        ->label('forms.form_type')->translateLabel()
                        ->options(FormTypeEnum::class)
                        ->disabledOn('edit')
                        ->live()->required(),
                    TextInput::make('name')
                        ->label('forms.name')->translateLabel()
                        ->disabledOn('edit')
                        ->required(),
//                    TextInput::make('ipca_value')
//                        ->label('IPCA')
//                        ->mask('99.99')
//                        ->postfix('%')
                ]),
                Repeater::make('taxes')
                    ->label('Taxas')
                    ->collapsible()->collapsed()
                    ->cloneable()->addActionLabel('Adicionar Banco')
                    ->hidden(fn (Get $get): bool => !$get('type'))
                    ->schema(fn(Get $get) => match ($get('type')) {
                        FormTypeEnum::CONSORTIUM->value => [self::consortiumForm()],
                        FormTypeEnum::REAL_ESTATE_CREDIT->value,
                        FormTypeEnum::CREDIT_PROPERTY_GUARANTEE->value => [self::realEstateCreditForm()],
                        default => []
                    })
                    ->itemLabel(fn (array $state): ?string => isset($state['bank_id']) ? Bank::find($state['bank_id'], 'name')->name : null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('forms.form_type')->translateLabel(),

                TextColumn::make('name')
                    ->label('forms.name')->translateLabel()
                    ->sortable(),
            ])
            ->actions([
                Action::make('open')
                    ->label('Abrir')
                    ->icon('heroicon-o-link')
                    ->url(fn (FormModel $record) => $record->url)
                    ->openUrlInNewTab(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
//            'create' => Pages\CreateForm::route('/create'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }

    private static function consortiumForm(): Grid
    {
        return Grid::make(3)
            ->schema([
                Grid::make(1)->schema([
                    Toggle::make('is_active')->label('Ativo')->default(true),
                ]),
                Select::make('bank_id')
                    ->label('Banco')
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->required()
                    ->options(Bank::orderBy('name')->pluck('name', 'id')),
                TextInput::make('tax_admin')
                    ->columnSpan(1)
                    ->label('Taxa de Administração')
                    ->mask('99.99')
                    ->postfix('%')
                    ->required(),
                TextInput::make('reserva')
                    ->label('Reserva')
                    ->mask('99.99')
                    ->postfix('%')
                    ->required()
            ]);
    }

    private static function realEstateCreditForm(): Grid
    {
        return Grid::make(3)->schema([
            Grid::make()->schema([
                Toggle::make('is_active')->label('Ativo')->default(true),
            ]),
            Select::make('bank_id')
                ->label('Banco')
                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                ->required()
                ->options(Bank::orderBy('name')->pluck('name', 'id')),
            TextInput::make('jurosEfetiva')
                ->label('Juros Efetiva')
                ->mask('99.99')
                ->postfix('%')
                ->required(),
            TextInput::make('jurosEfetiva_display')
                ->label('Juros Efetiva (Exibição)'),
            TextInput::make('ltv')
                ->label('LTV')
                ->required(),
            TextInput::make('avaliacao')
                ->label('Avaliação')
                ->required()
        ]);
    }
}
