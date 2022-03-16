<?php

namespace App\Http\Controllers\V1\Hotel\Habitacion;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\V1\Hotel\HEstado;
use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Hotel\HHabitacion;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Query\Builder;
use App\Http\Controllers\ApiController;
use App\Models\V1\Hotel\HHabitacionFoto;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HHabitacionPrecio;
use App\Models\V1\Hotel\HReservacionDetalle;
use Intervention\Image\Exception\ImageException;

class Habitacion extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $otras_tablas = array('estado', 'precio_habitacion', 'habitacion');
            $columna    = $this->traInformacion($request['sortBy']) ? $request['sortBy'] : "description";
            $criterio   = $this->traInformacion($request['search']) ? $request['search'] : null;
            $orden      = $this->traInformacion($request['sortDesc']) ? $request['sortDesc'] : 'asc';
            $filas      = $request['perPage'];
            $pagina     = $request['page'];

            $consulta = HHabitacion::with('estado', 'precios.tipo_cama')
                ->when(!is_null($criterio) && !in_array($criterio, $otras_tablas), function ($query) use ($columna, $criterio) {
                    $query->where($columna, 'LIKE', "%{$criterio}%");
                })
                ->when(!is_null($criterio) && $criterio == 'estado', function ($query) use ($criterio) {
                    $query->whereHas('estado', function (Builder $query) use ($criterio) {
                        $query->where('nombre', 'LIKE', "%{$criterio}%");
                    });
                })
                ->when(!is_null($criterio) && $criterio == 'precio_habitacion', function ($query) use ($criterio) {
                    $query->whereHas('precios', function (Builder $query) use ($criterio) {
                        $query->where('precio', 'LIKE', "%{$criterio}%");
                    });
                })
                ->when(!is_null($criterio) && $criterio == 'habitacion', function ($query) use ($criterio) {
                    $query->whereHas('precios', function (Builder $query) use ($criterio) {
                        $query->with('tipo_cama')->whereHas('tipo_cama', function (Builder $query) use ($criterio) {
                            $query->where('nombre', 'LIKE', "%{$criterio}%");
                        });
                    });
                })
                ->orderBy($columna, $orden)
                ->skip($pagina)
                ->take($filas)
                ->get();

            $data = array(
                'total' => count($consulta),
                'data' => $consulta,
            );

            return $this->successResponse($data);
        } catch (\Throwable $th) {
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

            $cantidad = HHabitacion::count();

            $habitacion = new HHabitacion();
            $habitacion->foto = $path;
            $habitacion->numero = $cantidad + 1;
            $habitacion->huespedes = 0;
            $habitacion->description = $request->description;
            $habitacion->h_estados_id = HEstado::DISPONIBLE;
            $habitacion->save();

            $huespedes = 0;
            foreach ($request->precios as $value) {
                $huespedes += HTipoCama::find($value['h_tipos_camas_id'])->cantidad;

                HHabitacionPrecio::create(
                    ['price' => $value['price'], 'activo' => true, 'h_tipos_camas_id' => $value['h_tipos_camas_id'], 'h_habitaciones_id' => $habitacion->id]
                );
            }

            $habitacion->huespedes = $huespedes;
            $habitacion->save();

            HHabitacionFoto::create(['foto' => $habitacion->foto, 'h_habitaciones_id' => $habitacion->id]);

            DB::commit();
            return $this->successResponse("Habitación: {$habitacion->description} fue registrado.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof ImageException) {
                return $this->errorResponse('Ocurrio un problema al grabar la fotografía de la habitación');
            } else if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al grabar la información de la habitación');
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
            $habitacion->description = $request->description;
            $habitacion->h_estados_id = $request->h_estados_id;
            $habitacion->save();
            $estado = HEstado::find($habitacion->h_estados_id)->nommbre;
            return $this->successResponse("Habitación: {$habitacion->description} fue actualizada y se encuentra en estado {$estado}.");
        } catch (\Exception $e) {
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
            if (HReservacionDetalle::where('h_reservaciones_id', $habitacion->id)->count() > 0) {
                return $this->errorResponse('La habitación no puede eliminarse.');
            }

            HHabitacionPrecio::where('h_reservaciones_id', $habitacion->id)->delete();
            $fotografias = HHabitacionFoto::where('h_reservaciones_id', $habitacion->id)->get();

            foreach ($fotografias as $value) {
                Storage::disk('habitacion')->exists($value->foto) ? Storage::disk('habitacion')->delete($value->foto) : null;
                $value->delete();
            }

            $habitacion->delete();
            return $this->successResponse("Habitación: {$habitacion->nombre} fue eliminado.");
        } catch (\Exception $e) {
            if ($e instanceof QueryException) {
                return $this->errorResponse('El registro se encuentra en uso');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }
}
