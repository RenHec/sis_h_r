<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HHabitacionPrecio extends Model
{
    use HasFactory;

    protected $table = 'h_habitaciones_precios';

    protected $fillable = ['precio', 'activo', 'h_tipos_camas_id', 'h_habitaciones_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'activo' => 'boolean',
        'precio' => 'float'
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
