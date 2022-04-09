<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    const COCINA = 1;
    const MESERO = 2;

    use HasFactory, SoftDeletes;

    protected $table = 'r_producto';

    protected $fillable = ['id', 'nombre', 'precio', 'img', 'activo', 'tipo_producto_id', 'costo', 'quien_prepara', 'usa_inventario', 'consumo_reservacion','descripcion','autoreferencia','promocion'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'consumo_reservacion' => 'boolean'
    ];

    public function producto_categoria_comida()
    {
        return $this->hasMany(ProductoCategoriaComida::class);
    }

    public function preparaCocina()
    {
        return $this->quien_prepara === Producto::COCINA;
    }
}
