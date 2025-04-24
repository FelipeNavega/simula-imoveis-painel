<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\TextEntry;

class TextBooleanEntry extends TextEntry
{
    public function boolean(?string $trueLabel = null, ?string $falseLabel = null): TextBooleanEntry
    {
        $this->formatStateUsing(static function ($state) use ($trueLabel, $falseLabel) {
            return $state ?
                $trueLabel ?? __('filament-forms::components.radio.boolean.true') :
                $falseLabel ?? __('filament-forms::components.radio.boolean.false');
        });

        return $this;
    }
}
