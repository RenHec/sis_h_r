<?php

namespace App\Models\V1\Hotel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HCheckIn extends Model
{
    use HasFactory;

    protected $table = 'h_check_in';

    protected $fillable = ['codigo', 'nombre', 'foto', 'lista', 'h_reservaciones_id', 'h_reservaciones_detalles_id', 'usuarios_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
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
        return Storage::disk('firma')->exists($this->foto) ? Storage::disk('firma')->url($this->foto) : null;
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
