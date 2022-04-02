<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoOrden extends Model
{
    use HasFactory;

    protected $table = 'r_tipo_orden';

    protected $fillable = ['id','nombre','reservacion'];
}
