<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'r_caja';

    protected $fillable = ['id','fecha_apertura','hora_apertura','fecha_cierre','hora_cierre','saldo_inicial','ingresos','egresos','activo','usuario_id'];
}
