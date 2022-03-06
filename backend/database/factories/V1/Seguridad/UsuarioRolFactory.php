<?php

namespace Database\Factories\V1\Seguridad;

use App\Models\V1\Seguridad\Rol;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Seguridad\UsuarioRol;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioRolFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UsuarioRol::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $usuario = Usuario::all()->random()->id;
        $rol = Rol::all()->random()->id;

        if (is_null(UsuarioRol::where('usuario_id', $usuario)->where('rol_id', $rol)->first())) {
            return [
                'usuario_id' => 1,
                'rol_id' => 1
            ];
        } else {
            return [];
        }
    }
}
