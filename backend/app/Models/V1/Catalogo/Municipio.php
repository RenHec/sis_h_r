<?php

namespace App\Models\V1\Catalogo;

use Illuminate\Database\Eloquent\Model;
use App\Models\V1\Catalogo\Departamento;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Municipio extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'municipios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'departamento_id', 'codigo_original', 'codigo'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        $departament = Departamento::find($this->departamento_id)->nombre;
        return str_replace('  ', ' ', "{$departament}, {$this->nombre}");
    }

    /**
     * Get the departamento associated with the municipality.
     *
     * @return object
     */
    public function departamento()
    {
        return $this->hasOne(Departamento::class, 'id', 'departamento_id');
    }
}
