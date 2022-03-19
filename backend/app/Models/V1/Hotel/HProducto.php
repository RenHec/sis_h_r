<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HKardex;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HProducto extends Model
{
    use HasFactory;

    protected $table = 'h_productos';

    protected $fillable = ['nombre', 'descripcion', 'consumible', 'activo', 'usuarios_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a',
        'consumible' => 'boolean',
        'activo' => 'boolean'
    ];

    /**
     * Get the usuario associated.
     *
     * @return object
     */
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'usuarios_id', 'id');
    }

    /**
     * Get the kardex associated.
     *
     * @return object
     */
    public function kardex()
    {
        return $this->hasOne(HKardex::class, 'h_productos_id', 'id');
    }
}
