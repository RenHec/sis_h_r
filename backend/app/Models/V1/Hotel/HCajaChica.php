<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Catalogo\Mes;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\V1\Hotel\HCajaChicaMovimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HCajaChica extends Model
{
    use HasFactory;

    protected $table = 'h_caja_chica';

    protected $fillable = [
        'inicio', 'finalizo', 'inicia_caja', 'pagos', 'insumos', 'compras', 'restaurante',
        'cierre_caja', 'anio', 'meses_id', 'dia', 'abierta', 'usuarios_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y h:i:s a',
        'updated_at' => 'datetime:d-m-Y h:i:s a',
        'inicio' => 'datetime:d/m/Y h:i:s a',
        'finalizo' => 'datetime:d/m/Y h:i:s a',
        'inicia_caja' => 'float',
        'pagos' => 'float',
        'insumos' => 'float',
        'compras' => 'float',
        'cierre_caja' => 'float',
        'restaurante' => 'float',
        'anio' => 'integer',
        'dia' => 'integer',
        'abierta' => 'boolean'
    ];

    /**
     * Get the mes associated.
     *
     * @return object
     */
    public function mes()
    {
        return $this->hasOne(Mes::class, 'id', 'meses_id');
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
     * Get the movimientos associated.
     *
     * @return array
     */
    public function movimientos()
    {
        return $this->hasMany(HCajaChicaMovimiento::class, 'h_caja_chica_id', 'id');
    }
}
