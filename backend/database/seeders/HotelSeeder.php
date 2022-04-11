<?php

namespace Database\Seeders;

use App\Imports\HabitacionesHotelImport;
use Illuminate\Database\Seeder;
use App\Models\V1\Hotel\HEstado;
use App\Models\V1\Hotel\HTipoCama;
use App\Models\V1\Hotel\HHabitacion;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\V1\Hotel\HKardexHistorial;
use App\Models\V1\Hotel\HHabitacionPrecio;

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

        Excel::import(new HabitacionesHotelImport, 'database/seeders/Catalogos/Habitaciones.xlsx');
    }
}
