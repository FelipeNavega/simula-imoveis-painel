<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MunicipiosEstados extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'municipios_estados_BR';

    protected $fillable = [
        'estado', 'uf', 'regiao', 'municipio', 'capital'
    ];

    public function getMunicipioEstadoAttribute(): string
    {
        return $this->municipio.'/'. $this->uf;
    }
}
