<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    const COCINA = 1;
    const MESERO = 2;

    use HasFactory;

    protected $table = 'r_producto';

    protected $fillable = ['id','nombre','precio','img','activo','tipo_producto_id','costo','quien_prepara'];

    public function producto_categoria_comida()
    {
        return $this->hasMany(ProductoCategoriaComida::class);
    }

    public function preparaCocina()
    {
        return $this->quien_prepara === Producto::COCINA;
    }
}
