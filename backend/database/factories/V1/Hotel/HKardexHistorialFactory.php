<?php

namespace Database\Factories\V1\Hotel;

use App\Models\V1\Hotel\HKardex;
use App\Models\V1\Hotel\HKardexHistorial;
use App\Models\V1\Hotel\HProducto;
use Illuminate\Database\Eloquent\Factories\Factory;

class HKardexHistorialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HKardexHistorial::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $producto = HProducto::create(
            [
                'nombre' => $this->faker->company,
                'descripcion' => $this->faker->catchPhrase,
                'consumible' => $this->faker->randomElement([true, false]),
                'activo' => true,
                'usuarios_id' => 1
            ]
        );
        $stock = $this->faker->numberBetween(0, 5000);
        $kardex = HKardex::create(
            [
                'stock_actual' => $stock,
                'stock_inicial' => $stock,
                'stock_consumido' => 0,
                'h_productos_id' => $producto->id,
                'usuarios_id'  => $producto->usuarios_id,
                'activo' => $stock > 0 ? true : false,
                'check_in' => $this->faker->randomElement([true, false])
            ]
        );

        return [
            'documento' => "Kardex - {$kardex->id}",
            'stock_anterior' => 0,
            'signo' => "=",
            'stock_nuevo' => $stock,
            'descripcion' => "Se creo el inventario para el producto {$producto->nombre}",
            'h_insumos_detalles_id' => 0,
            'h_check_in_id' => 0,
            'h_kardex_id' => $kardex->id,
            'usuarios_id' => $producto->usuarios_id
        ];
    }
}
