<?php

namespace App\Models\V1\Principal;

use App\Models\V1\Catalogo\Mes;
use App\Models\V1\Principal\Empresa;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Principal\CajaPago;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caja extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cajas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inicio',
        'finalizo',
        'inicia_caja',
        'venta_total',
        'compra_total',
        'creditos',
        'devolucion',
        'abierta',
        'meses_id',
        'usuarios_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a',
        'inicio' => 'datetime:d/m/Y h:i:s a',
        'finalizo' => 'datetime:d/m/Y h:i:s a',
        'abierta' => 'boolean',
        'inicia_caja' => 'float',
        'venta_total' => 'float',
        'compra_total' => 'float',
        'creditos' => 'float',
        'devolucion' => 'float'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'inicio', 'finalizo'];

    /**
     * Get the meses associated with the configuracion.
     *
     * @return array
     */
    public function meses()
    {
        return $this->hasOne(Mes::class, 'id', 'meses_id');
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

    /**
     * Get the usuarios associated with the configuracion.
     *
     * @return array
     */
    public function pagos()
    {
        return $this->hasOne(CajaPago::class, 'cajas_id', 'id');
    }
}
