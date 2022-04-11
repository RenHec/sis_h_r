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
    private $controlador_principal = 'HabitacionPrecioController';

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

            $precio_nuevo = HHabitacionPrecio::create(
                [
                    'cantidad_camas' => $request->cantidad_camas,
                    'nombre' => $request->nombre,
                    'precio_desayuno' => $request->incluye_desayuno ? $request->precio_desayuno : 0,
                    'precio_habitacion' => $request->precio_habitacion,
                    'precio' => $request->incluye_desayuno ? $request->precio_desayuno + $request->precio_habitacion : $request->precio_habitacion,
                    'activo' => true,
                    'incluye_desayuno' => $request->incluye_desayuno,
                    'h_tipos_camas_id' => $request->h_tipos_camas_id['id'],
                    'h_habitaciones_id' => $habitacion_precio->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $habitacion_precio->huespedes += (HTipoCama::find($request->h_tipos_camas_id['id'])->cantidad * $precio_nuevo->cantidad_camas);
            $habitacion_precio->save();

            $this->bitacora_general($precio_nuevo->getTable(), $this->acciones(0), $precio_nuevo, "{$this->controlador_principal}@update");
            $this->bitacora_general($habitacion_precio->getTable(), $this->acciones(1), $habitacion_precio, "{$this->controlador_principal}@update");

            DB::commit();
            return $this->successResponse("Precio para la habitación: {$habitacion_precio->description} fue registrada.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información del precio para la habitación');
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
            $habitacion->huespedes = $habitacion->huespedes - (HTipoCama::find($habitacion_precio->h_tipos_camas_id)->cantidad * $habitacion_precio->cantidad_camas);
            $habitacion->updated_at = date('Y-m-d H:i:s');
            $habitacion->save();

            $this->bitacora_general($habitacion->getTable(), $this->acciones(1), $habitacion, "{$this->controlador_principal}@destroy");

            $habitacion_precio->activo = $habitacion_precio->activo ? false : true;
            $habitacion_precio->updated_at = date('Y-m-d H:i:s');
            $habitacion_precio->save();

            $this->bitacora_general($habitacion_precio->getTable(), $this->acciones(1), $habitacion_precio, "{$this->controlador_principal}@destroy");
            DB::commit();
            return $this->successResponse("El precio de la habitación fue eliminado.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            if ($e instanceof QueryException) {
                $mensaje = $habitacion_precio->activo ? "desactivar" : "activar";
                return $this->errorResponse("Ocurrio un problema al {$mensaje} la información del precio para la habitación");
            }
            return $this->errorResponse('Error en el controlador');
        }
    }
}
