<?php

namespace App\Models\V1\Hotel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HReservacionDetalle;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HCheckOut extends Model
{
    use HasFactory;

    protected $table = 'h_check_out';

    protected $fillable = ['codigo', 'nombre', 'foto', 'lista', 'descripcion', 'habitacion', 'h_reservaciones_id', 'h_reservaciones_detalles_id', 'h_check_in_id', 'usuarios_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
        'lista' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['firma'];

    /**
     * Get the user's link base64 foto.
     *
     * @return string
     */
    public function getFirmaAttribute()
    {
        return Storage::disk('firma')->exists("{$this->codigo}/{$this->foto}") ? Storage::disk("{$this->codigo}/{$this->foto}")->url($this->foto) : null;
    }

    /**
     * Get the detalle associated.
     *
     * @return object
     */
    public function detalle()
    {
        return $this->hasOne(HReservacionDetalle::class, 'id', 'h_reservaciones_detalles_id');
    }
}
