<?php

namespace App\Models\V1\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'nombre_ruta',
        'padre',
        'mostrar',
        'icono'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y h:i:s a',
        'updated_at' => 'datetime:d-m-Y h:i:s a',
        'mostrar' => 'boolean'
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
    protected $appends = ['principal'];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getPrincipalAttribute()
    {
        $menu = Menu::find($this->padre);
        return !is_null($menu) ? $menu->id : 0;
    }
}
