<?php

namespace App\Models\V1\Hotel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HReservacionDetalle extends Model
{
    use HasFactory;

    protected $table = 'h_reservaciones_detalles';

    protected $fillable = ['codigo', 'inicio', 'fin', 'dias', 'horas', 'huespedes', 'precio', 'sub_total', 'descripcion', 'disponible', 'h_reservaciones_id', 'h_habitaciones_precios_id', 'h_habitaciones_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a',
        'inicio' => 'datetime:d/m/Y h:i:s a',
        'fin' => 'datetime:d/m/Y h:i:s a',
        'sub_total' => 'float',
        'dias' => 'integer',
        'horas' => 'integer',
        'huespedes' => 'integer',
        'precio' => 'float',
        'sub_total' => 'float',
        'disponible' => 'boolean'
    ];
}
