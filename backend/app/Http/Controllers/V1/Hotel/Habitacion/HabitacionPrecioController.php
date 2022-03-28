<?php

namespace App\Http\Controllers\V1\Hotel\Habitacion;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Hotel\HHabitacion;
use Illuminate\Database\QueryException;
use App\Models\V1\Hotel\HHabitacionPrecio;

class HabitacionPrecioController extends ApiController
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Hotel\HHabitacion  $habitacion_precio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HHabitacion $habitacion_precio)
    {
        try {
            DB::beginTransaction();

            HHabitacionPrecio::create(
                [
                    'precio' => $request->precio, 'activo' => true, 'h_tipos_camas_id' => $request->h_tipos_camas_id['id'], 'h_habitaciones_id' => $habitacion_precio->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $habitacion_precio->huespedes += HTipoCama::find($request->h_tipos_camas_id['id'])->cantidad;
            $habitacion_precio->save();

            DB::commit();
            return $this->successResponse("Precio para la habitación: {$habitacion_precio->description} fue registrada.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al grabar la información del precio para la habitación');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HHabitacionPrecio  $habitacion_precio
     * @return \Illuminate\Http\Response
     */
    public function destroy(HHabitacionPrecio $habitacion_precio)
    {
        try {
            DB::beginTransaction();
            $habitacion = HHabitacion::find($habitacion_precio->h_habitaciones_id);
            $habitacion->huespedes = $habitacion->huespedes - HTipoCama::find($habitacion_precio->h_tipos_camas_id)->cantidad;
            $habitacion->save();

            $habitacion_precio->activo = $habitacion_precio->activo ? false : true;
            $habitacion_precio->save();
            DB::commit();
            return $this->successResponse("El precio de la habitación fue eliminado.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error en el controlador');
        }
    }
}
