<?php

namespace App\Http\Controllers\V1\Hotel\Reservacion;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\V1\Hotel\HKardex;
use App\Models\V1\Hotel\HCheckIn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Hotel\HReservacion;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\ApiController;
use App\Models\V1\Hotel\HHabitacion;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\ImageException;

class CheckIn extends ApiController
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\V1\Hotel\HCheckIn  $check_in
     * @return \Illuminate\Http\Response
     */
    public function show(HCheckIn $check_in)
    {
        try {
            return $this->successResponse($check_in->detalle);
        } catch (\Exception $e) {
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Store the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $img = $this->getB64Image($request->foto);
            $image = Image::make($img);
            $image->fit(870, 620, function ($constraint) {
                $constraint->upsize();
            });
            $image->encode('png', 70);
            $nombre = Str::random(10);
            $path = "{$request->codigo}/check/{$nombre}.png";
            Storage::disk('firma')->put($path, $image);

            DB::beginTransaction();

            if (!is_null(HCheckIn::where('codigo', $request->codigo)->first())) {
                throw new \Exception("La reservación con código {$request->codigo} ya cuenta con el check in registrado.", 1);
            };

            $producto_entregar = array();
            $h_reservaciones_id = null;
            foreach ($request->check_in as $check_in) {
                $check = HCheckIn::create(
                    [
                        'codigo' => $request->codigo,
                        'nombre' => $request->nombre,
                        'foto' => $path,
                        'lista' => $check_in['lista'],
                        'descripcion' => $request->descripcion,
                        'h_reservaciones_id' => $check_in['h_reservaciones_id'],
                        'h_reservaciones_detalles_id' => $check_in['h_reservaciones_detalles_id'],
                        'usuarios_id' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );

                foreach ($check->lista as $agregado) {
                    $kardex = HKardex::find($agregado['id']);

                    if (is_null($kardex)) {
                        throw new \Exception("El producto {$agregado['producto']} no esta registrado y no cuenta con inventario registrado.", 1);
                    }

                    $cantidad = intval($agregado['cantidad']);

                    if (($kardex->stock_actual - $cantidad) < 0) {
                        throw new \Exception("El producto {$agregado['producto']} no tiene el stock suficiente para la rservación con código {$check->codigo}.", 1);
                    }

                    $kardex->stock_actual -= $cantidad;
                    $kardex->activo = $kardex->stock_actual > 0 ? true : false;
                    $kardex->stock_consumido += $cantidad;
                    $kardex->updated_at = date('Y-m-d H:i:s');
                    $kardex->save();

                    $this->historial_kardex("i", $kardex->stock_actual, $cantidad, $check->id, $kardex, $agregado['producto']);

                    /*REGISTRAR EL PRODUCTO QUE TIENE QUE ENTREGAR AL HUSPED*/
                    $data['kardex'] = $kardex->id;
                    $data['producto'] = $agregado['producto'];
                    $data['distribucion'] = array();
                    $data['total'] = 0;

                    $distribucion['habitacion'] = HHabitacion::find($check_in['h_habitaciones_id'])->numero;
                    $distribucion['cantidad'] = $cantidad;

                    $actualizo = false;
                    foreach ($producto_entregar as $ingresar) {
                        if ($kardex->id === $ingresar['kardex']) {
                            array_push($ingresar['distribucion'], $distribucion);
                            $ingresar['total'] += $distribucion['cantidad'];
                            $actualizo = true;
                        }
                    }

                    if (!$actualizo) {
                        array_push($data['distribucion'], $distribucion);
                        array_push($data, $producto_entregar);
                    }
                }

                $h_reservaciones_id = $check->h_reservaciones_id;
            }

            HReservacion::where('id', $h_reservaciones_id)->update(['check_in' => true]);

            DB::commit();

            return $this->successResponse(['mensaje' => "Check In: se realizó el check in para la reservación con código {$request->codigo}.", 'lista' => $producto_entregar]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof ImageException) {
                return $this->errorResponse('Ocurrio un problema al grabar la firma del check in');
            } else if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al registrar la información del check in.');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HCheckIn  $check_in
     * @return \Illuminate\Http\Response
     */
    public function destroy(HCheckIn $check_in)
    {
        //
    }
}
