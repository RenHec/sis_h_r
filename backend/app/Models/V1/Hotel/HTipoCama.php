<?php

namespace App\Models\V1\Hotel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HTipoCama extends Model
{
    use HasFactory;

    protected $table = 'h_tipos_camas';

    protected $fillable = ['nombre', 'cantidad'];
}
