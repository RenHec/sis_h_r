<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'r_orden';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = ['id','monto','estado_pedido_id','fecha','hora','cliente_id','mesa_id','usuario_id'];
}
