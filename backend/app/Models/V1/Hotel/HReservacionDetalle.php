<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HHabitacion;
use App\Models\V1\Hotel\HReservacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HReservacionDetalle extends Model
{
    use HasFactory;

    protected $table = 'h_reservaciones_detalles';

    protected $fillable = [
        'codigo', 'inicio', 'fin', 'dias', 'horas', 'huespedes',
        'precio', 'sub_total', 'descripcion', 'disponible', 'h_reservaciones_id',
        'h_habitaciones_precios_id', 'h_habitaciones_id', 'incluye_desayuno'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y',
        'inicio' => 'datetime:d/m/Y h:i:s a',
        'fin' => 'datetime:d/m/Y h:i:s a',
        'sub_total' => 'float',
        'dias' => 'integer',
        'horas' => 'integer',
        'huespedes' => 'integer',
        'precio' => 'float',
        'sub_total' => 'float',
        'disponible' => 'boolean',
        'incluye_desayuno' => 'boolean'
    ];

    /**
     * Get the habitacion associated.
     *
     * @return object
     */
    public function habitacion()
    {
        return $this->hasOne(HHabitacion::class, 'id', 'h_habitaciones_id');
    }

    /**
     * Get the reservacion associated.
     *
     * @return object
     */
    public function reservacion()
    {
        return $this->hasOne(HReservacion::class, 'id', 'h_reservaciones_id');
    }
}
