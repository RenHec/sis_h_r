<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HProducto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HInsumoDetalle extends Model
{
    use HasFactory;

    protected $table = 'h_insumos_detalles';

    protected $fillable = ['documento', 'producto', 'cantidad', 'precio', 'descuento', 'sub_total', 'h_insumos_id', 'h_productos_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y h:i:s a',
        'precio' => 'float',
        'descuento' => 'float',
        'sub_total' => 'float'
    ];

    /**
     * Get the producto associated.
     *
     * @return object
     */
    public function producto()
    {
        return $this->hasOne(HProducto::class, 'h_productos_id', 'id');
    }
}
