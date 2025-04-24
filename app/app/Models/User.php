<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\PartnerTypeEnum;
use App\Enums\UserTypeEnum;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'type', 'document', 'name', 'email', 'password', 'extra', 'phone', 'is_active', 'partner_type'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'type' => UserTypeEnum::class,
        'partner_type' => PartnerTypeEnum::class
    ];

    protected static function boot(): void
    {
        parent::boot();
        self::creating(static function (self $model) {
            try {
                $model->partner_code = $model::genSingleCode();
            } catch (\Throwable) {
                $model->partner_code = '';
            }
            $model->document = preg_replace('/\D/', '', $model->document);
            $model->phone = preg_replace('/\D/', '', $model->phone);
        });
        self::updating(static function (self $model) {
            $model->document = preg_replace('/\D/', '', $model->document);
            $model->phone = preg_replace('/\D/', '', $model->phone);
        });
        self::deleting(static function (self $model) {
            $model->simulations()->delete();
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->type === UserTypeEnum::ADMIN,
            'partner' => $this->type === UserTypeEnum::PARTNER && $this->is_active,
        };
    }

    public function scopeSearchNumericValues(Builder $query, string $field, string $value): Builder
    {
        $value =  sprintf("%%%s%%", preg_replace('/\D/', '', $value));
        return $query->where($field, 'like', $value);
    }

    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    public function simulation(): HasOne
    {
        return $this->hasOne(Simulation::class, 'created_by');
    }

    public function simulations(): HasMany
    {
        return $this->hasMany(Simulation::class, 'created_by');
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

    public static function genSingleCode(): string
    {
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = Str::random(8);

            $existingUser = self::where('partner_code', $code)->exists();

            if (!$existingUser) {
                return $code;
            }
        }

        // Se o loop exceder o número máximo de tentativas, lança uma exceção
        throw new \RuntimeException('Não foi possível gerar um código único após várias tentativas.');
    }
}
