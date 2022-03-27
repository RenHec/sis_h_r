<?php

namespace App\Models\V1\Hotel;

use App\Models\V1\Hotel\HCheckIn;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Hotel\HInsumoDetalle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HKardexHistorial extends Model
{
    use HasFactory;

    protected $table = 'h_kardex_historial';

    protected $fillable = ['documento', 'stock_anterior', 'signo', 'stock_nuevo', 'descripcion', 'tabla_id', 'h_kardex_id', 'usuarios_id', 'tabla', 'eliminado'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'stock_anterior' => 'integer',
        'stock_nuevo' => 'integer',
        'eliminado' => 'boolean'
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
}
