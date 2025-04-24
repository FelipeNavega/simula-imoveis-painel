<?php

namespace App\Filament\Resources;

use App\Enums\PartnerTypeEnum;
use App\Enums\UserTypeEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Mail\UserApprovedMail;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'partners';

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?string $pluralLabel = 'Parceiros';

    protected static ?string $label = 'Parceiros';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('partner_type')
                    ->label('general.partner_type')->translateLabel()
                    ->options(PartnerTypeEnum::class),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->required(),

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
                    )),
                TextInput::make('social_name')
                    ->label('general.social_name')->translateLabel(),

                TextInput::make('phone')
                    ->label('forms.phone')->translateLabel()
                    ->mask(RawJs::make(<<<'JS'
                        $input.replaceAll(/\D/g, '').length <= 10 ? '(99) 9999-9999' : '(99) 99999-9999'
                    JS
                    )),

                Grid::make()
                    ->schema([
                        TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn($state): bool => filled($state))
                            ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),

                        TextInput::make('passwordConfirmation')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->visible(fn(Get $get): bool => filled($get('password')))
                            ->dehydrated(false),
                    ]),

                Toggle::make('is_active')
                    ->inline(false)
                    ->label('Status'),

                Grid::make()
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('general.created_at')->translateLabel()
                            ->content(fn(?User $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('general.updated_at')->translateLabel()
                            ->content(fn(?User $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('partner_type')
                    ->label('forms.questions.partner_type')->translateLabel()
                    ->toggleable(),

                TextColumn::make('partner_code')
                    ->label('general.partner_code')->translateLabel()
                    ->searchable()
                    ->toggleable()
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('forms.name')->translateLabel()
                    ->searchable(isIndividual: true)
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('forms.email')->translateLabel()
                    ->searchable(query: fn($query, $search) => $query->where('email', $search))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('document_with_mask')
                    ->label('forms.document')->translateLabel()
                    ->searchable(query: fn($query, $search) => $query->searchNumericValues('document', $search))
                    ->toggleable(),

                TextColumn::make('phone_with_mask')
                    ->label('forms.phone')->translateLabel()
                    ->searchable(query: fn($query, $search) => $query->searchNumericValues('phone', $search))
                    ->toggleable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->toggleable()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return $record->is_active ? 'Ativo' : 'Inativo';
                    })
                    ->color(fn(string $state): string => $state === 'Ativo' ? 'success' : 'gray'),

                TextColumn::make('simulations_count')
                    ->getStateUsing(function (User $record) {
//                        dd($record, $record->simulations_count);
                        return $record->simulations_count;
                    })
                    ->label('Total de simulações')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Ativo',
                        '0' => 'Inativo',
                    ]),
                Tables\Filters\SelectFilter::make('partner_type')
                    ->label('general.partner_type')->translateLabel()
                    ->options(PartnerTypeEnum::class),
                Tables\Filters\Filter::make('created_at')
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
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->hidden(fn(User $record): bool => $record->type === 'partner'),
                    Tables\Actions\Action::make('approve_user')
                        ->icon('heroicon-c-shield-check')
                        ->hidden(fn(User $record): bool => $record->is_active)
                        ->label('Aprovar')
                        ->action(function (User $record) {
                            $record->update(['is_active' => true]);
                            Mail::to($record)->send(new UserApprovedMail($record));
                            Notification::make()
                                ->title('Usuário aprovado com sucesso!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('partner_code')
                        ->icon('heroicon-o-arrow-path')
                        ->hidden(fn(User $record): bool => !blank($record->partner_code))
                        ->label('Gerar Código de Parceiro')
                        ->action(fn(User $record) => self::actionGenerateCode($record)),
                ])->icon('heroicon-m-ellipsis-horizontal')
            ])
            ->groups([
                Tables\Grouping\Group::make('simulations_count')
                    ->label('Total de simulações'),
                Tables\Grouping\Group::make('partner_type')
                    ->label(__('general.partner_type')),
                Tables\Grouping\Group::make('is_active')
                    ->label('Status')
                    ->getTitleFromRecordUsing(fn($record) => $record->is_active ? 'Ativo' : 'Inativo'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', UserTypeEnum::PARTNER);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function actionGenerateCode(User $record): void
    {
        try {
            $record->partner_code = $record::genSingleCode();
            $record->save();

            Notification::make()
                ->title('Codigo gerado com sucesso')
                ->success()
                ->send();
        } catch (\Throwable) {
            Notification::make()
                ->title('Erro ao gerar o Código')
                ->danger()
                ->send();
        }
    }
}
