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
                $precio = 200;
                break;
            case 3:
                $precio = 250;
                break;
            case 4:
                $precio = 300;
                break;
        }

        $habitacion = HHabitacion::all()->random();
        $habitacion->huespedes += $tipo->cantidad;
        $habitacion->save();
        return [
            'precio' => $precio,
            'activo' => true,
            'h_tipos_camas_id' => $tipo->id,
            'h_habitaciones_id' => $habitacion->id
        ];
    }
}
