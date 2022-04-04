<?php

namespace App\Models\V1\Principal;

use App\Models\V1\Catalogo\Municipio;
use Illuminate\Database\Eloquent\Model;
use App\Models\V1\Catalogo\Departamento;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = ['nit', 'nombre', 'telefonos', 'emails', 'direcciones', 'departamentos_id', 'municipios_id', 'usuarios_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a'
    ];

    /**
     * Set the proveedores nit.
     *
     * @param  string  $value
     * @return void
     */
    public function setNitAttribute($value)
    {
        $this->attributes['nit'] = str_replace([" ", "-"], ["", ""], $value);
    }

    /**
     * Scope a query to only include softDelted
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTodos($query)
    {
        return $query->with('departamento', 'municipio')->withTrashed()->orderByDesc('id');
    }

    /**
     * Get the departamento associated with the user.
     *
     * @return object
     */
    public function departamento()
    {
        return $this->hasOne(Departamento::class, 'id', 'departamentos_id');
    }

    /**
     * Get the municipio associated with the user.
     *
     * @return object
     */
    public function municipio()
    {
        return $this->hasOne(Municipio::class, 'id', 'municipios_id');
    }
}
