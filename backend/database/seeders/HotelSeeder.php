<?php

namespace Database\Seeders;

use App\Models\V1\Hotel\HEstado;
use App\Models\V1\Hotel\HHabitacion;
use App\Models\V1\Hotel\HHabitacionPrecio;
use App\Models\V1\Hotel\HKardexHistorial;
use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HEstado::insert([
            ['nombre' => 'DISPONIBLE'],
            ['nombre' => 'MANTENIMIENTO'],
            ['nombre' => 'INACTIVO']
        ]);

        HTipoCama::insert([
            ['nombre' => 'Individual', 'cantidad' => 1],
            ['nombre' => 'Matrimonial', 'cantidad' => 2],
            ['nombre' => 'Queen', 'cantidad' => 2],
            ['nombre' => 'King', 'cantidad' => 2]
        ]);

        HHabitacion::factory(10)->create();
        HHabitacionPrecio::factory(30)->create();
        HKardexHistorial::factory(250)->create();
    }
}
