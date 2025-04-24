<?php

namespace App\Filament\Resources\SimulationResource\Infolist;

use Filament\Infolists\{Components, Infolist};

final class Consortium
{
    public static function infolist(Infolist $infolist)
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
                Components\Tabs\Tab::make('Perguntas')
                    ->schema([
                        Components\TextEntry::make('answers.consortium_value')
                            ->label('forms.questions.consortium_value')->translateLabel()
                            ->money('BRL'),
                        Components\TextEntry::make('answers.months_pay')
                            ->label('forms.questions.months_pay')->translateLabel()
                            ->suffix(' Meses'),
                        Components\TextEntry::make('localization.state')
                            ->label('forms.localization.state')->translateLabel()
                            ->getStateUsing(fn($record) => $record->municipiosEstados->estado),
                        Components\TextEntry::make('localization.city')
                            ->label('forms.localization.city')->translateLabel()
                            ->getStateUsing(fn($record) => $record->municipiosEstados->municipio),
                        Components\TextEntry::make('answers.monthly_income')
                            ->label('forms.questions.monthly_income')->translateLabel()
                            ->money('BRL'),
                    ])
            ]);
    }
}
