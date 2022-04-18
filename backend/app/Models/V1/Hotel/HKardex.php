<?php

namespace App\Models\V1\Hotel;

use Illuminate\Database\Eloquent\Model;
use App\Models\V1\Hotel\HKardexHistorial;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HKardex extends Model
{
    use HasFactory;

    protected $table = 'h_kardex';

    protected $fillable = ['stock_actual', 'stock_inicial', 'stock_consumido', 'h_productos_id', 'usuarios_id', 'activo', 'check_in'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y',
        'stock_actual' => 'integer',
        'stock_inicial' => 'integer',
        'stock_consumido' => 'integer',
        'activo' => 'boolean',
        'check_in' => 'boolean'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['check_in_tag'];

    /**
     * Get the user's link base64 foto.
     *
     * @return string
     */
    public function getCheckInTagAttribute()
    {
        return $this->check_in ? 'SI, aplica para el check in' : 'NO, aplica para el check in';
    }

    /**
     * Get the producto associated.
     *
     * @return object
     */
    public function producto()
    {
        return $this->hasOne(HProducto::class, 'id', 'h_productos_id');
    }

    /**
     * Get the usuario associated.
     *
     * @return object
     */
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id', 'usuarios_id');
    }

    /**
     * Get the historial associated.
     *
     * @return array
     */
    public function historial()
    {
        return $this->hasMany(HKardexHistorial::class, 'h_kardex_id', 'id')->orderBy('created_at', 'DESC');
    }
}
