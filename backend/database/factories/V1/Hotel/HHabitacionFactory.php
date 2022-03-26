<?php

namespace Database\Factories\V1\Hotel;

use App\Models\V1\Hotel\HHabitacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class HHabitacionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HHabitacion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'foto' => null,
            'numero' => $this->faker->unique()->numerify('###'),
            'huespedes' => 0,
            'descripcion' => "Habitación de prueba",
            'h_estados_id' => 1
        ];
    }
}
