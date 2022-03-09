<?php

namespace App\Models\V1\Restaurante;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaComida extends Model
{
    use HasFactory;

    protected $table = 'categoria_comida';

    protected $fillable = ['id','nombre'];
}
