<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Principal\Proveedor;
use App\Models\V1\Hotel\HInsumoDetalle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HInsumo extends Model
{
    use HasFactory;

    protected $table = 'h_insumos';

    protected $fillable = ['documento', 'sub_total', 'descuento', 'total', 'anulado', 'nombre_proveedor', 'proveedores_id', 'usuarios_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y h:i:s a',
        'updated_at' => 'datetime:d-m-Y h:i:s a',
        'sub_total' => 'float',
        'descuento' => 'float',
        'total' => 'float',
        'anulado' => 'boolean'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['anula'];

    /**
     * Get the eliminar pago.
     *
     * @return bool
     */
    public function getAnulaAttribute()
    {
        $fecha_hoy = date('d-m-Y');
        $created_at = date('d-m-Y', strtotime($this->created_at));
        return strtotime($created_at) == strtotime($fecha_hoy);
    }

    /**
     * Get the proveedor associated.
     *
     * @return object
     */
    public function proveedor()
    {
        return $this->hasOne(Proveedor::class, 'id', 'proveedores_id');
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
        return $this->hasMany(HInsumoDetalle::class, 'h_insumos_id', 'id');
    }
}
