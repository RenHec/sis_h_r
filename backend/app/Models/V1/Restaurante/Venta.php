<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'r_venta';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = ['id','orden_id','tipo_pago_id','cliente_id','usuario_id','monto','voucher','venta_id'];
}
