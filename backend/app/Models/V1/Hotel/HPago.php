<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Hotel\HReservacion;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HPago extends Model
{
    use HasFactory;

    protected $table = 'h_pagos';

    protected $fillable = ['nit', 'nombre', 'ubicacion', 'vaucher_pago', 'detalle', 'factura', 'sub_total', 'descuento', 'total', 'h_reservaciones_id', 'tipos_pagos_id', 'usuarios_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'factura' => 'boolean',
        'sub_total' => 'float',
        'descuento' => 'float',
        'total' => 'float',
        'detalle' => 'array'
    ];

    /**
     * Get the reservacion associated.
     *
     * @return object
     */
    public function reservacion()
    {
        return $this->hasOne(HReservacion::class, 'id', 'h_reservaciones_id');
    }

    /**
     * Get the tipo_pago associated.
     *
     * @return object
     */
    public function tipo_pago()
    {
        return $this->hasOne(TipoPago::class, 'id', 'tipos_pagos_id');
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
}
