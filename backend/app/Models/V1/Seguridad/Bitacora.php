<?php

namespace App\Models\V1\Seguridad;

use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bitacora extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bitacoras';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tabla',
        'accion',
        'descripcion',
        'controlador',
        'usuario',
        'usuarios_id',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'controlador'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d m Y H:i:s',
        'updated_at' => 'datetime:d m Y H:i:s'
    ];

    /**
     * Get the usuarios associated with the configuracion.
     *
     * @return array
     */
    public function usuarios()
    {
        return $this->hasOne(Usuario::class, 'id', 'usuarios_id');
    }
}
