<?php

namespace App\Imports;

use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Hotel\HHabitacion;
use App\Models\V1\Hotel\HHabitacionPrecio;
use Maatwebsite\Excel\Concerns\ToCollection;

class HabitacionesHotelImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $value) {
            HTipoCama::firstOrCreate(
                [
                    'nombre' => 'INDIVIDUAL',
                    'cantidad' => 1
                ]
            );

            if (!is_null($value) && $key > 0) {
                DB::beginTransaction();
                $tipo_cama = HTipoCama::firstOrCreate(
                    [
                        'nombre' => $value[2],
                        'cantidad' => $value[3]
                    ]
                );

                $habitacion = HHabitacion::create(
                    [
                        'foto' => null,
                        'numero' => $value[0],
                        'huespedes' => 0,
                        'descripcion' => $value[4],
                        'h_estados_id' => 1
                    ]
                );

                $precio = HHabitacionPrecio::create(
                    [
                        'cantidad_camas' => $value[1],
                        'nombre' => $value[5],
                        'precio_desayuno' => intval($value[6]),
                        'precio_habitacion' => intval($value[7]),
                        'precio' => intval($value[6]) + intval($value[7]),
                        'activo' => true,
                        'h_tipos_camas_id' => $tipo_cama->id,
                        'h_habitaciones_id' => $habitacion->id,
                        'incluye_desayuno' => $value[8] == "SI"
                    ]
                );

                $habitacion->huespedes += ($tipo_cama->cantidad * $precio->cantidad_camas);
                $habitacion->save();

                while ($value[1] > 1) {
                    $value[1] = $value[1] - 1;

                    $precio = 0;
                    switch ($value[1]) {
                        case 1:
                            $precio = 165;
                            break;

                        case 2:
                            $precio = 325;
                            break;
                    }

                    HHabitacionPrecio::create(
                        [
                            'cantidad_camas' => $value[1],
                            'nombre' => 'Normal',
                            'precio_desayuno' => intval($value[6]),
                            'precio_habitacion' => $precio,
                            'precio' => $precio + intval($value[6]),
                            'activo' => true,
                            'h_tipos_camas_id' => $tipo_cama->id,
                            'h_habitaciones_id' => $habitacion->id,
                            'incluye_desayuno' => true
                        ]
                    );
                }

                HHabitacionPrecio::create(
                    [
                        'cantidad_camas' => 1,
                        'nombre' => 'Persona Extra',
                        'precio_desayuno' => 0,
                        'precio_habitacion' => 100,
                        'precio' => 100,
                        'activo' => true,
                        'h_tipos_camas_id' => 1,
                        'h_habitaciones_id' => $habitacion->id,
                        'incluye_desayuno' => false
                    ]
                );
                DB::commit();
            }
        }
    }
}
