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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['ingreso'];

    public function getIngresotAttribute()
    {
        return date('d-m-Y H:i:s', strtotime($this->created_at));
    }

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
