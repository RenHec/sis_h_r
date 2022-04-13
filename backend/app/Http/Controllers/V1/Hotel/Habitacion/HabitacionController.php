<?php

namespace App\Http\Controllers\V1\Hotel\Habitacion;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\V1\Hotel\HEstado;
use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Hotel\HHabitacion;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\ApiController;
use App\Models\V1\Hotel\HHabitacionFoto;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HHabitacionPrecio;
use App\Models\V1\Hotel\HReservacionDetalle;
use Intervention\Image\Exception\ImageException;

class HabitacionController extends ApiController
{
    private $controlador_principal = 'HabitacionController';

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse(HHabitacion::with('estado', 'precios.tipo_cama', 'imagenes')->orderByDesc('id')->get());
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@index");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $img = $this->getB64Image($request->foto);
            $image = Image::make($img);
            $image->encode('jpg', 70);
            $nombre = Str::random(10);
            $path = "{$nombre}.jpg";
            Storage::disk('habitacion')->put($path, $image);

            DB::beginTransaction();

            $habitacion = new HHabitacion();
            $habitacion->foto = $path;
            $habitacion->numero = $request->numero;
            $habitacion->huespedes = 0;
            $habitacion->descripcion = $request->descripcion;
            $habitacion->h_estados_id = HEstado::DISPONIBLE;
            $habitacion->created_at = date('Y-m-d H:i:s');
            $habitacion->save();

            $habitacion_precio = HHabitacionPrecio::create(
                [
                    'cantidad_camas' => $request->cantidad_camas,
                    'nombre' => $request->nombre,
                    'precio_desayuno' => $request->incluye_desayuno ? $request->precio_desayuno : 0,
                    'precio_habitacion' => $request->precio_habitacion,
                    'precio' => $request->incluye_desayuno ? $request->precio_desayuno + $request->precio_habitacion : $request->precio_habitacion,
                    'activo' => true,
                    'incluye_desayuno' => $request->incluye_desayuno,
                    'h_tipos_camas_id' => $request->h_tipos_camas_id['id'],
                    'h_habitaciones_id' => $habitacion->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            HHabitacionFoto::create(['foto' => $habitacion->foto, 'h_habitaciones_id' => $habitacion->id]);

            $habitacion->huespedes = HTipoCama::find($request->h_tipos_camas_id['id'])->cantidad * $habitacion_precio->cantidad_camas;
            $habitacion->save();

            $this->bitacora_general($habitacion->getTable(), $this->acciones(0), $habitacion, "{$this->controlador_principal}@store");
            $this->bitacora_general($habitacion_precio->getTable(), $this->acciones(0), $habitacion_precio, "{$this->controlador_principal}@store");

            DB::commit();
            return $this->successResponse("Habitación: {$habitacion->descripcion} fue registrado.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof ImageException) {
                return $this->errorResponse('Ocurrio un problema al grabar la fotografía de la habitación');
            } else if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información de la habitación');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Hotel\HHabitacion  $habitacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HHabitacion $habitacion)
    {
        try {
            DB::beginTransaction();
            $habitacion->numero = $request->numero;
            $habitacion->descripcion = $request->descripcion;
            $habitacion->h_estados_id = $request->h_estados_id['id'];
            $habitacion->updated_at = date('Y-m-d H:i:s');
            $habitacion->save();

            $this->bitacora_general($habitacion->getTable(), $this->acciones(1), $habitacion, "{$this->controlador_principal}@update");
            DB::commit();
            return $this->successResponse("Habitación: {$habitacion->descripcion} fue actualizada y se encuentra en estado {$request->h_estados_id['nombre']}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al actualizar la información de la habitación');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HHabitacion  $habitacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(HHabitacion $habitacion)
    {
        try {
            DB::beginTransaction();

            if (HReservacionDetalle::whereIn('h_habitaciones_precios_id', HHabitacionPrecio::where('h_habitaciones_id', $habitacion->id)->pluck('id'))->count() > 0) {
                return $this->errorResponse('La habitación no puede eliminarse.');
            }

            $precios = HHabitacionPrecio::where('h_habitaciones_id', $habitacion->id)->get();
            foreach ($precios as $item) {
                $item->delete();
                $this->bitacora_general($item->getTable(), $this->acciones(2), $item, "{$this->controlador_principal}@destroy");
            }

            $fotografias = HHabitacionFoto::where('h_habitaciones_id', $habitacion->id)->get();

            foreach ($fotografias as $value) {
                Storage::disk('habitacion')->exists($value->foto) ? Storage::disk('habitacion')->delete($value->foto) : null;
                $value->delete();
            }

            $habitacion->delete();

            $this->bitacora_general($habitacion->getTable(), $this->acciones(2), $habitacion, "{$this->controlador_principal}@destroy");

            DB::commit();
            return $this->successResponse("Habitación: {$habitacion->descripcion} fue eliminado.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al eliminar la información de la habitación.');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }
}
