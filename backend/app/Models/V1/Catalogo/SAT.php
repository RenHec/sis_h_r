<?php

namespace App\Models\V1\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SAT extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sat_nit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nit',
        'cui',
        'primerNombre',
        'segundoNombre',
        'tercerNombre',
        'primerApellido',
        'segundoApellido',
        'apellidoCasada',
        'fechaModifico',
        'correlativo',
        'digito',
        'codigo_departamento',
        'codigo_municipio'
    ];
}
