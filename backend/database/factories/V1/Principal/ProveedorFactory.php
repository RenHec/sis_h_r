<?php

namespace Database\Factories\V1\Principal;

use App\Models\V1\Catalogo\Municipio;
use App\Models\V1\Principal\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Proveedor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $municipio = Municipio::all()->random();
        return [
            'nit' => $this->faker->unique()->numerify('########'),
            'nombre' => $this->faker->randomElement([$this->faker->firstNameMale, $this->faker->firstNameFemale]),
            'telefonos' => $this->faker->randomElement([$this->faker->unique()->numerify('########'), null]),
            'emails' => $this->faker->randomElement([$this->faker->unique()->freeEmail, null]),
            'direcciones' => $this->faker->randomElement([$this->faker->address, null]),
            'departamentos_id' => $municipio->departamento_id,
            'municipios_id' => $municipio->id,
            'usuarios_id' => 1
        ];
    }
}
