<?php

namespace Database\Factories\V1\Hotel;

use App\Models\V1\Hotel\HHabitacion;
use App\Models\V1\Hotel\HHabitacionPrecio;
use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Database\Eloquent\Factories\Factory;

class HHabitacionPrecioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HHabitacionPrecio::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tipo = HTipoCama::all()->random();
        $precio = 0;
        switch ($tipo->id) {
            case 1:
                $precio = 100;
                break;
            case 2:
                $precio = 150;
                break;
            case 3:
                $precio = 200;
                break;
            case 4:
                $precio = 250;
                break;
        }

        $habitacion = HHabitacion::all()->random();
        $habitacion->huespedes += $tipo->cantidad;
        $habitacion->save();

        $incuye = $tipo->id != 1 ? $this->faker->randomElement([true, false]) : false;
        $precio_desayuno = $incuye ? $this->faker->numberBetween(10, 25) : 0;
        $precio_total = ($tipo->cantidad * $precio_desayuno) + $precio;

        return [
            'nombre' => $this->faker->company,
            'precio_desayuno' => $precio_desayuno,
            'precio_habitacion' => $precio,
            'precio' => $precio_total,
            'activo' => true,
            'incluye_desayuno' => $incuye,
            'h_tipos_camas_id' => $tipo->id,
            'h_habitaciones_id' => $habitacion->id,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
}
