<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HEstado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HHabitacionFoto;
use App\Models\V1\Hotel\HHabitacionPrecio;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HHabitacion extends Model
{
    use HasFactory;

    protected $table = 'h_habitaciones';

    protected $fillable = ['foto', 'numero', 'huespedes', 'descripcion', 'h_estados_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['picture'];

    /**
     * Get the user's link base64 foto.
     *
     * @return string
     */
    public function getPictureAttribute()
    {
        return Storage::disk('habitacion')->exists($this->foto) ? Storage::disk('habitacion')->url($this->foto) : null;
    }

    /**
     * Get the estado associated.
     *
     * @return object
     */
    public function estado()
    {
        return $this->belongsTo(HEstado::class, 'h_estados_id', 'id');
    }

    /**
     * Get the precios associated.
     *
     * @return array
     */
    public function precios()
    {
        return $this->hasMany(HHabitacionPrecio::class, 'h_habitaciones_id', 'id');
    }

    /**
     * Get the imagenes associated.
     *
     * @return array
     */
    public function imagenes()
    {
        return $this->hasMany(HHabitacionFoto::class, 'h_habitaciones_id', 'id');
    }
}
