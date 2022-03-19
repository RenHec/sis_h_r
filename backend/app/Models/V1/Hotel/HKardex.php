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
        'updated_at' => 'datetime:d/m/Y h:i:s a',
        'stock_actual' => 'integer',
        'stock_inicial' => 'integer',
        'stock_consumido' => 'integer',
        'activo' => 'boolean',
        'check_in' => 'boolean'
    ];

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
        return $this->hasMany(HKardexHistorial::class, 'h_kardex_id', 'id');
    }
}
