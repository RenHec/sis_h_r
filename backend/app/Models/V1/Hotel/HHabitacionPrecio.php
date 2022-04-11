<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HHabitacionPrecio extends Model
{
    use HasFactory;

    protected $table = 'h_habitaciones_precios';

    protected $fillable = [
        'nombre', 'precio_desayuno', 'precio_habitacion', 'precio',
        'activo', 'h_tipos_camas_id', 'h_habitaciones_id', 'incluye_desayuno',
        'cantidad_camas'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'activo' => 'boolean',
        'precio_desayuno' => 'float',
        'precio_habitacion' => 'float',
        'precio' => 'float',
        'incluye_desayuno' => 'boolean',
        'cantidad_camas' => 'integer'
    ];

    /**
     * Get the tipo_cama associated.
     *
     * @return object
     */
    public function tipo_cama()
    {
        return $this->hasOne(HTipoCama::class, 'id', 'h_tipos_camas_id');
    }
}
