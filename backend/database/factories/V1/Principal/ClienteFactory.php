<?php

namespace Database\Factories\V1\Principal;

use App\Models\V1\Principal\Cliente;
use App\Models\V1\Catalogo\Municipio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cliente::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $municipio = Municipio::all()->random();
        $direccion = $this->faker->randomElement([$this->faker->address, null]);
        return [
            'nit' => $this->faker->unique()->numerify('########'),
            'nombre' => $this->faker->randomElement([$this->faker->firstNameMale, $this->faker->firstNameFemale]),
            'telefonos' => $this->faker->randomElement([$this->faker->unique()->numerify('########'), null]),
            'emails' => $this->faker->randomElement([$this->faker->unique()->freeEmail, null]),
            'direcciones' => $direccion,
            'departamentos_id' => $municipio->departamento_id,
            'municipios_id' => $municipio->id,
            'usuarios_id' => 1
        ];
    }
}
