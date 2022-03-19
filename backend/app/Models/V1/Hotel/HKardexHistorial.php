<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HCheckIn;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Hotel\HInsumoDetalle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HKardexHistorial extends Model
{
    use HasFactory;

    protected $table = 'h_kardex_historial';

    protected $fillable = ['documento', 'stock_anterior', 'signo', 'stock_nuevo', 'descripcion', 'h_insumos_detalles_id', 'h_check_in_id', 'h_kardex_id', 'usuarios_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'stock_anterior' => 'integer',
        'stock_nuevo' => 'integer'
    ];

    /**
     * Get the insumo associated.
     *
     * @return object
     */
    public function insumo()
    {
        return $this->hasOne(HInsumoDetalle::class, 'h_insumos_detalles_id', 'id');
    }

    /**
     * Get the reservacion associated.
     *
     * @return object
     */
    public function reservacion()
    {
        return $this->hasOne(HCheckIn::class, 'h_check_in_id', 'id');
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
}
