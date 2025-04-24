<?php

namespace App\Filament\Resources\SimulationResource\Infolist;

use App\Enums\AmortizationSystemEnum;
use App\Enums\PropertyStateEnum;
use App\Enums\PropertyTypeEnum;
use App\Infolists\Components\TextBooleanEntry;
use Filament\Infolists\{Components, Infolist};

final class CreditPropertyGuarantee
{
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                self::getTabs(),
                Components\Grid::make(3)
                    ->schema([
                        Components\TextEntry::make('partner.name')
                            ->label('forms.partner_name')->translateLabel()
                            ->getStateUsing(fn($record) => $record->partner->name ?? '-'),
                        Components\TextEntry::make('created_at')
                            ->label('general.created_at')->translateLabel()
                            ->getStateUsing(fn($record) => $record->created_at->format('d/m/Y H:i')),
                        Components\TextEntry::make('form_type')
                            ->label('forms.form_type')->translateLabel(),
                    ])
            ]);
    }

    public static function getTabs(): Components\Tabs
    {
        return Components\Tabs::make('tabs')
            ->columnSpanFull()
            ->columns()
            ->tabs([
                Components\Tabs\Tab::make('forms.tabs.customer')->translateLabel()
                    ->schema([
                        Components\TextEntry::make('name')
                            ->label('forms.name')->translateLabel(),
                        Components\TextEntry::make('email')
                            ->label('forms.email')->translateLabel(),
                        Components\TextEntry::make('document_with_mask')
                            ->label('forms.document')->translateLabel(),
                        Components\TextEntry::make('phone_with_mask')
                            ->label('forms.phone')->translateLabel(),
                        Components\TextEntry::make('answers.date_of_birth')
                            ->label('forms.questions.date_of_birth')->translateLabel(),
                    ]),
                Components\Tabs\Tab::make('forms.tabs.property_details')->translateLabel()
                    ->schema([
                        Components\TextEntry::make('answers.property_type')
                            ->label('forms.questions.property_type')->translateLabel()
                            ->formatStateUsing(fn ($record) => PropertyTypeEnum::getAndLabel($record->answers['property_type'])),
                        Components\TextEntry::make('answers.property_state')
                            ->formatStateUsing(fn ($record) => PropertyStateEnum::getAndLabel($record->answers['property_state']))
                            ->label('forms.questions.property_state')->translateLabel(),
                        Components\TextEntry::make('answers.property_value')
                            ->label('forms.questions.property_value')->translateLabel()
                            ->money('BRL'),
                        Components\TextEntry::make('answers.loan_value')
                            ->label('forms.questions.loan_value')->translateLabel()
                            ->money('BRL'),
                        Components\TextEntry::make('answers.installments')
                            ->label('forms.questions.installments')->translateLabel()
                            ->getStateUsing(fn($record) => sprintf('%s meses', $record->answers['installments'])),
                        Components\TextEntry::make('answers.amortization_system')
                            ->formatStateUsing(fn ($record) => AmortizationSystemEnum::getAndLabel($record->answers['amortization_system']))
                            ->label('forms.questions.amortization_system')->translateLabel(),
                        Components\TextEntry::make('localization.state')
                            ->label('forms.localization.state')->translateLabel()
                            ->getStateUsing(fn($record) => $record->municipiosEstados->estado),
                        Components\TextEntry::make('localization.city')
                            ->label('forms.localization.city')->translateLabel()
                            ->getStateUsing(fn($record) => $record->municipiosEstados->municipio),
                    ]),
                Components\Tabs\Tab::make('Perguntas')
                    ->columns(1)
                    ->schema([
                        TextBooleanEntry::make('answers.property_already_paid')
                            ->label('forms.questions.property_already_paid')->translateLabel()
                            ->boolean(),
                    ])
            ]);
    }
}
