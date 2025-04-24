<?php

namespace App\Models;

use App\Enums\AmortizationSystemEnum;
use App\Enums\FormTypeEnum;
use App\Enums\PropertyStateEnum;
use App\Enums\PropertyTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Simulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by', 'municipios_estados_id', 'name', 'email', 'phone',
        'document', 'property_type', 'property_state', 'property_value', 'amortization_system',
        'answers', 'form_type', 'entry_value', 'installments',
    ];

    protected $casts = [
        'answers' => 'array',
        'property_value' => 'float',
        'entry_value' => 'float',
        'property_state' => PropertyStateEnum::class,
        'property_type' => PropertyTypeEnum::class,
        'amortization_system' => AmortizationSystemEnum::class,
        'form_type' => FormTypeEnum::class,
    ];

    protected static function boot(): void
    {
        parent::boot();
        self::creating(static function (self $simulation) {
            $simulation->document = preg_replace('/\D/', '', $simulation->document);
            $simulation->phone = preg_replace('/\D/', '', $simulation->phone);
        });
        self::created(static function (self $simulation) {
            if($simulation->created_by) {
                $simulation->partner()->increment('simulations_count');
            }
        });
        self::deleted(static function (self $simulation) {
            if($simulation->created_by) {
                $simulation->partner()->decrement('simulations_count');
            }
        });
    }

    public function scopeByState(Builder $query, string $stateUf): Builder
    {
        return $query->whereIn('municipios_estados_id',
            fn($query) => $query->select('id')->from('municipios_estados_BR')->where('uf', $stateUf)
        );
    }

    public function scopeSearchNumericValues(Builder $query, string $field, string $value): Builder
    {
        $value =  sprintf("%%%s%%", preg_replace('/\D/', '', $value));
        return $query->where($field, 'like', $value);
    }

    public function scopeHasAnswer(Builder $query, string $key, $value): Builder
    {
        return $query->where('answers->'.$key, $value);
    }

    public function scopeCreatedBy(Builder $query, $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function municipiosEstados(): BelongsTo
    {
        return $this->belongsTo(MunicipiosEstados::class);
    }

    public function getDocumentWithMaskAttribute(): string
    {
        if (strlen($this->document) > 11) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->document);
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->document);
    }

    public function getPhoneWithMaskAttribute(): string
    {
        return preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $this->phone);
    }
}
