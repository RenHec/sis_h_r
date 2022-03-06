<?php

namespace App\Models\V1\Principal;

use App\Models\V1\Principal\Caja;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Principal\Empresa;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CajaPago extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cajas_pagos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'monto_total',
        'tipos_pagos_id',
        'cajas_id',
        'empresas_id',
        'usuarios_id'
    ];

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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['monto'];

    /**
     * Get the configuration link base64 logo.
     *
     * @return string
     */
    public function getMontoAttribute()
    {
        return "Q" . number_format($this->monto_total, 2, '.', ',');
    }

    /**
     * Get the tipos_pagos associated with the configuracion.
     *
     * @return array
     */
    public function tipos_pagos()
    {
        return $this->hasOne(TipoPago::class, 'id', 'tipos_pagos_id');
    }

    /**
     * Get the cajas associated with the configuracion.
     *
     * @return array
     */
    public function cajas()
    {
        return $this->hasOne(Caja::class, 'id', 'cajas_id');
    }

    /**
     * Get the empresas associated with the configuracion.
     *
     * @return array
     */
    public function empresas()
    {
        return $this->hasOne(Empresa::class, 'id', 'empresas_id');
    }

    /**
     * Get the usuarios associated with the configuracion.
     *
     * @return array
     */
    public function usuarios()
    {
        return $this->hasOne(Usuario::class, 'id', 'usuarios_id');
    }
}
