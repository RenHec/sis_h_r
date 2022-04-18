<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HCajaChicaMovimiento extends Model
{
    use HasFactory;

    const EFECTIVO = "EFECTIVO";
    const TARJETA = "TARJETA";
    const CHEQUE = "CHEQUE";

    protected $table = 'h_caja_chica_movimiento';

    protected $fillable = [
        'descripcion', 'monto_total', 'tipo_pago', 'comprobante',
        'usuarios_id', 'h_caja_chica_id', 'resta', 'registro_manual',
        'eliminado', 'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'monto_total' => 'float',
        'resta' => 'boolean',
        'registro_manual' => 'boolean',
        'eliminado' => 'boolean'
    ];

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
