<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HPago;
use App\Models\V1\Hotel\HCheckIn;
use App\Models\V1\Hotel\HCheckOut;
use App\Models\V1\Principal\Cliente;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HReservacionDetalle;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HReservacion extends Model
{
    use HasFactory;

    protected $table = 'h_reservaciones';

    protected $fillable = [
        'codigo', 'nombre', 'sub_total', 'extra', 'total', 'reservacion',
        'check_in', 'check_out', 'pagado', 'anulado', 'clientes_id', 'usuarios_id', 'comprobante'
    ];

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['ticket'];

    /**
     * Get the user's link base64 foto.
     *
     * @return string
     */
    public function getTicketAttribute()
    {
        return Storage::disk('ticket')->exists($this->comprobante) ? Storage::disk('ticket')->url($this->comprobante) : null;
    }

    /**
     * Scope a query to only include resarvacion
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTodas($query) //Todas
    {
        return $query->with('cliente', 'usuario', 'detalle', 'check_in_list', 'check_out_list', 'pago')
            ->where('anulado', false)
            ->orderByDesc('id');
    }

    /**
     * Scope a query to only include resarvacion
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReservacion($query) //Lista para pasar a CheckIn
    {
        return $query->with('detalle.habitacion')
            ->where('reservacion', true)
            ->where('check_in', false)
            ->where('check_out', false)
            ->where('anulado', false)
            ->orderBy('id');
    }

    /**
     * Scope a query to only include check_in
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIn($query) //Lista para pasar a CheckOut
    {
        return $query->with('check_in_list')
            ->where('reservacion', true)
            ->where('check_in', true)
            ->where('check_out', false)
            ->where('anulado', false)
            ->orderBy('created_at', 'DESC');
    }

    /**
     * Scope a query to only include check_out
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOut($query) //Lista para pasar a Pago
    {
        return $query->with('check_in_list', 'check_out_list', 'detalle', 'cliente.municipio')
            ->where('reservacion', true)
            ->where('pagado', false)
            ->where('anulado', false)
            ->orderBy('id');
    }

    /**
     * Scope a query to only include pagado
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnulado($query)
    {
        return $query->where('anulado', true)
            ->orderByDesc('id');
    }

    /**
     * Get the cliente associated.
     *
     * @return object
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'clientes_id');
    }

    /**
     * Get the usuario associated.
     *
     * @return object
     */
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id', 'usuarios_id');
    }

    /**
     * Get the detalle associated.
     *
     * @return array
     */
    public function detalle()
    {
        return $this->hasMany(HReservacionDetalle::class, 'h_reservaciones_id', 'id');
    }

    /**
     * Get the check_in associated.
     *
     * @return array
     */
    public function check_in_list()
    {
        return $this->hasMany(HCheckIn::class, 'h_reservaciones_id', 'id');
    }

    /**
     * Get the check_out associated.
     *
     * @return array
     */
    public function check_out_list()
    {
        return $this->hasMany(HCheckOut::class, 'h_reservaciones_id', 'id');
    }

    /**
     * Get the pago associated.
     *
     * @return object
     */
    public function pago()
    {
        return $this->hasOne(HPago::class, 'h_reservaciones_id', 'id')->where('anulado', false);
    }
}
