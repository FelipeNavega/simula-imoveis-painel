<?php

namespace App\Filament\Pages\Forms;

use Carbon\Carbon;
use Filament\Forms\Get;

trait BirthdayLimitValidation
{
    public function checkBirthdayValid(): \Closure
    {
        return static function (string $attribute, $birthday, \Closure $fail) {
            $birthday = Carbon::createFromFormat('Y-m-d H:i:s', $birthday);

            if ($birthday->isFuture() || $birthday->isToday() || $birthday->diffInYears(Carbon::now()) < 18) {
                $fail(trans('forms.invalid_birthday'));
            }
        };
    }

    public function checkBirthdayRange(Get $get): \Closure
    {
        return static function (string $attribute, $monthsPay, \Closure $fail) use ($get) {
            $birthday = Carbon::createFromFormat('Y-m-d H:i:s', $get('date_of_birth'));
            $now = Carbon::now();

            $birthdaySub = $birthday->copy()->subMonths($monthsPay);

            $diffYears = $birthdaySub->diffInYears($now);
            $diffMonths = $birthdaySub->diffInMonths($now);

            if ($diffYears > 80 || ($diffYears === 80 && $diffMonths > 6)) {
                $fail(trans('forms.birthday_limit'));
            }
        };
    }

    public function checkMaxInstallmentValue(Get $get): \Closure
    {
        return static function (string $attribute, $monthsPay, \Closure $fail) use ($get) {
            $birthday = Carbon::createFromFormat('Y-m-d H:i:s', $get('date_of_birth'));
            $currentAge = $birthday->diffInYears(Carbon::now());
            $differYears = 80 - $currentAge;

            $installments = Carbon::now()->addMonths($monthsPay + 6);
            $differInstallments = $birthday->diffInYears($installments);

            if($differInstallments < 80) {
                return;
            }

            $newDate = $birthday->clone()->addYears($differYears);
            $maxInstallments = $newDate->diffInMonths($birthday);

            if($maxInstallments < $monthsPay) {
                $fail(trans('forms.max_installment_value', ['value' => $maxInstallments]));
            }
        };
    }
}
