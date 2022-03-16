<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Principal\Cliente;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Hotel\HReservacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HReservacion extends Model
{
    use HasFactory;

    protected $table = 'h_reservaciones';

    protected $fillable = ['codigo', 'nombre', 'sub_total', 'extra', 'total', 'reservacion', 'check_in', 'check_out', 'pagado', 'anulado', 'clientes_id', 'usuarios_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a',
        'sub_total' => 'float',
        'extra' => 'float',
        'total' => 'float',
        'reservacion' => 'boolean',
        'check_in' => 'boolean',
        'check_out' => 'boolean',
        'pagado' => 'boolean',
        'anulado' => 'boolean'
    ];

    /**
     * Scope a query to only include resarvacion
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReservacion($query)
    {
        return $query->with('cliente', 'usuario')
            ->where('reservacion', true)
            ->where('check_in', false)
            ->where('check_out', false)
            ->where('anulado', false)
            ->orderByDesc('id');
    }

    /**
     * Scope a query to only include check_in
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCheckIn($query)
    {
        return $query->with('cliente', 'usuario')
            ->where('reservacion', true)
            ->where('check_in', true)
            ->where('check_out', false)
            ->orderByDesc('id');
    }

    /**
     * Scope a query to only include check_out
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCheckOut($query)
    {
        return $query->with('cliente', 'usuario')
            ->where('reservacion', true)
            ->where('check_in', true)
            ->where('check_out', true)
            ->where('pagado', false)
            ->orderByDesc('id');
    }

    /**
     * Scope a query to only include pagado
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePagado($query)
    {
        return $query->with('cliente', 'usuario')
            ->where('reservacion', true)
            ->where('check_in', true)
            ->where('check_out', true)
            ->where('pagado', true)
            ->orderByDesc('id');
    }

    /**
     * Get the cliente associated.
     *
     * @return object
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'clientes_id', 'id');
    }

    /**
     * Get the usuario associated.
     *
     * @return object
     */
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'usuarios_id', 'id');
    }

    /**
     * Get the detalle associated.
     *
     * @return array
     */
    public function detalle()
    {
        return $this->hasMany(HReservacion::class, 'h_reservaciones_id', 'id');
    }
}
