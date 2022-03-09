<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoCategoriaComida extends Model
{
    use HasFactory;

    protected $table = 'producto_categoria_comida';

    protected $fillable = ['id','categoria_comida_id','producto_id'];
}
