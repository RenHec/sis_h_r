<?php

namespace App\Models\V1\Hotel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HEstado extends Model
{
    use HasFactory;

    const DISPONIBLE = 1;
    const MANTENIMIENTO = 2;
    const INACTIVO = 3;

    protected $table = 'h_estados';

    protected $fillable = ['nombre'];
}
