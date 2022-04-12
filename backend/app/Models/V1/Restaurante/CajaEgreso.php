<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaEgreso extends Model
{
    use HasFactory;

    protected $table = 'r_caja_egreso';

    protected $fillable = ['id','descripcion','monto','comprobante','caja_id','usuario_id'];
}
