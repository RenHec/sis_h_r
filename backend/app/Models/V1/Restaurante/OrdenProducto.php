<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenProducto extends Model
{
    use HasFactory;

    protected $table = 'orden_producto';

    protected $fillable = ['id','pedido_id','producto_id','cantidad','notas'];
}
