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
            $path = "{$request->codigo}/check/CHECKIN{$nombre}.png";
            Storage::disk('firma')->put($path, $image);

            DB::beginTransaction();

            if (!is_null(HCheckIn::where('codigo', $request->codigo)->first())) {
                throw new \Exception("La reservación con código {$request->codigo} ya cuenta con el check in registrado.", 1);
            };

            $producto_entregar = array();
            $h_reservaciones_id = null;
            foreach ($request->check_in as $key => $check_in) {
                $check = HCheckIn::create(
                    [
                        'codigo' => $request->codigo,
                        'nombre' => $request->nombre,
                        'foto' => $path,
                        'lista' => $check_in['lista'],
                        'descripcion' => $request->descripcion,
                        'habitacion' => $check_in['habitacion'],
                        'h_reservaciones_id' => $check_in['h_reservaciones_id'],
                        'h_reservaciones_detalles_id' => $check_in['h_reservaciones_detalles_id'],
                        'usuarios_id' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );

                if ($key == 0) {
                    $distribucion_check = $check;
                }

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

                    $this->historial_kardex("-ri", $kardex->stock_actual, $cantidad, $check->id, $kardex, $agregado['producto'], $check->getTable(), "Reservación {$check->codigo} - {$check->habitacion} (CheckIn)");

                    /*REGISTRAR EL PRODUCTO QUE TIENE QUE ENTREGAR AL HUSPED*/
                    $data['kardex'] = $kardex->id;
                    $data['producto'] = $agregado['producto'];
                    $data['consumible'] = $agregado['consumible'];
                    $data['distribucion'] = array();
                    $data['total'] = $cantidad;

                    $distribucion['habitacion'] = HHabitacion::find($check_in['h_habitaciones_id'])->numero;
                    $distribucion['cantidad'] = $cantidad;

                    $actualizo = false;
                    for ($i = 0; $i < count($producto_entregar); $i++) {
                        if ($kardex->id === $producto_entregar[$i]['kardex']) {
                            array_push($producto_entregar[$i]['distribucion'], $distribucion);
                            $producto_entregar[$i]['total'] += $distribucion['cantidad'];
                            $actualizo = true;
                        }
                    }

                    if (!$actualizo) {
                        array_push($data['distribucion'], $distribucion);
                        array_push($producto_entregar, $data);
                    }
                }

                $h_reservaciones_id = $check->h_reservaciones_id;
            }

            HReservacion::where('id', $h_reservaciones_id)->update(['check_in' => true]);

            $distribucion_check->distribucion = $producto_entregar;
            $distribucion_check->save();

            DB::commit();

            return $this->successResponse(['mensaje' => "Check In: se realizó el check in para la reservación con código {$request->codigo}."]);
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HReservacion  $check_in
     * @return \Illuminate\Http\Response
     */
    public function destroy(HReservacion $check_in)
    {
        try {
            DB::beginTransaction();

            $checks = HCheckIn::where('h_reservaciones_id', $check_in->id)->get();

            $foto = null;
            foreach ($checks as $item) {
                foreach ($item->lista as $agregado) {
                    $kardex = HKardex::find($agregado['id']);

                    if (is_null($kardex)) {
                        throw new \Exception("El producto {$agregado['producto']} no esta registrado y no cuenta con inventario registrado.", 1);
                    }

                    $cantidad = intval($agregado['cantidad']);

                    $kardex->stock_actual += $cantidad;
                    $kardex->activo = $kardex->stock_actual > 0 ? true : false;
                    $kardex->stock_consumido -= $cantidad;
                    $kardex->updated_at = date('Y-m-d H:i:s');
                    $kardex->save();

                    $this->historial_kardex("aci", $kardex->stock_actual, $cantidad, $item->h_reservaciones_id, $kardex, $agregado['producto'], $item->getTable(), "Reservación {$item->codigo} - {$item->habitacion} (CheckIn)", true);
                }

                $item->delete();
                $foto = $item->foto;
            }

            $check_in->check_in = false;
            $check_in->save();

            Storage::disk('firma')->exists($foto) ? Storage::disk('firma')->delete($foto) : null;

            DB::commit();

            return $this->successResponse("Check In: se realizó la anulación del check in para la reservación con código {$check_in->codigo}.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof ImageException) {
                return $this->errorResponse('Ocurrio un problema al anular la firma del check in');
            } else if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al anular la información del check in.');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }
}
