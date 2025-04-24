<?php

namespace App\Enums;

trait BaseEnum
{
    public static function getAndLabel(string $value): ?string
    {
        $enum = self::from($value);

        if(!blank($enum)) {
            return $enum->getLabel();
        }

        return null;
    }
}
