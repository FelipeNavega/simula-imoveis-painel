<?php

namespace App\Models;

use App\Enums\FormTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Form extends Model
{
    protected $fillable = [
        'type', 'name', 'slug', 'taxes'
    ];

    protected $casts = [
        'taxes' => 'collection',
        'type' => FormTypeEnum::class
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function (Form $form) {
            $slug = Str::slug($form->name);

            if(self::whereSlug($slug)->exists()) {
                $last = self::select('id')->orderByDesc('created_at')->get()->first();
                $slug .= '-' . $last?->id;
            }

            $form->slug = $slug;
        });
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => match ($this->type) {
                FormTypeEnum::CONSORTIUM => route('simulations.consortium'),
                FormTypeEnum::CREDIT_PROPERTY_GUARANTEE => route('simulations.credit-property-guarantee'),
                FormTypeEnum::REAL_ESTATE_CREDIT => route('simulations.real-estate-credit'),
                default => null
            },
        );
    }

    public function bankData(): array
    {
        Cache::forget('form-' . $this->getKey());
        return Cache::sear('form-' . $this->getKey(), function () {
            $taxes = $this->taxes->filter(fn ($tax) => $tax['is_active']);
            $bankIds = $taxes->unique('bank_id')->map(fn ($tax) => $tax['bank_id'])->toArray();
            $banks = Bank::select(['id', 'name', 'logo', 'slug'])->whereIn('id', $bankIds)->get()
                ->sortBy(fn($bank) => array_search($bank->getKey(), $bankIds));

            return [
                $banks->mapWithKeys(fn($bank) => [
                    $bank->slug => $bank->only('name', 'logo')
                ])->toArray(),
                $banks->mapWithKeys(function ($bank) use ($taxes) {
                    $tax = $taxes->where('bank_id', $bank->getKey())->first();
                    return [
                        $bank->slug => match ($this->type) {
                            FormTypeEnum::CONSORTIUM => $this->consortiumData($tax),
                            FormTypeEnum::REAL_ESTATE_CREDIT,
                            FormTypeEnum::CREDIT_PROPERTY_GUARANTEE => $this->realEstateCreditData($tax),
                        }
                    ];
                })->toArray()
            ];
        });
    }

    private function consortiumData(array $tax): array
    {
        return [
            'tax_admin' => $tax['tax_admin'],
            'reserva' => $tax['reserva']
        ];
    }

    public function realEstateCreditData(array $tax): array
    {
        return [
            'jurosEfetiva' => $tax['jurosEfetiva'],
            'jurosEfetiva_display' => $tax['jurosEfetiva_display'],
            'ltv' => $tax['ltv'],
            'avaliacao' => $tax['avaliacao']
        ];
    }
}
