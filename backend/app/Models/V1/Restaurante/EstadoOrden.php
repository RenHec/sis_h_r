<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoOrden extends Model
{
    use HasFactory;

    protected $table = 'estado_orden';

    protected $fillable = ['id','nombre','icono','inicia','finaliza','color'];
}
