<?php

namespace Database\Factories\V1\Seguridad;

use App\Models\V1\Catalogo\Departamento;
use App\Models\V1\Catalogo\Municipio;
use App\Models\V1\Principal\Empleado;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Usuario::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $empleado = Empleado::create(
            [
                'cui' => $this->faker->unique()->randomElement(['0000000000001', '0000000000002', '0000000000003', '0000000000004', '0000000000005', '0000000000006', '0000000000007']),
                'primer_nombre' => $this->faker->randomElement([$this->faker->firstNameMale, $this->faker->firstNameFemale]),
                'segundo_nombre' => $this->faker->randomElement([$this->faker->firstNameMale, $this->faker->firstNameFemale, null]),
                'primer_apellido' => $this->faker->lastName,
                'segundo_apellido' => $this->faker->randomElement([$this->faker->lastName, null]),
                'email' => $this->faker->unique()->freeEmail,
                'ubicacion' => $this->faker->randomElement([$this->faker->address, null]),
                'telefono' => $this->faker->unique()->numerify('########'),
                'departamento_id' => Departamento::all()->random()->id,
                'municipio_id' => Municipio::all()->random()->id
            ]
        );

        $admin = Usuario::where('admin', Usuario::USUARIO_ADMINISTRADOR)->first();
        $password = $empleado->cui === '0000000000001' ? 'admin' : 'admin123';

        return [
            'cui' => $empleado->cui,
            'admin' => is_null($admin) ? Usuario::USUARIO_ADMINISTRADOR : Usuario::USUARIO_REGULAR,
            'password' => $password,
            'empleado_id' => $empleado->id
        ];
    }
}
