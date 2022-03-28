<?php

namespace App\Http\Controllers\V1\Hotel\Reservacion;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\V1\Hotel\HKardex;
use App\Models\V1\Hotel\HCheckOut;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Hotel\HReservacion;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\ApiController;
use App\Models\V1\Hotel\HReservacionDetalle;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\ImageException;

class CheckOut extends ApiController
{
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
            $image->fit(870, 620, function ($constraint) {
                $constraint->upsize();
            });
            $image->encode('png', 70);
            $nombre = Str::random(10);
            $path = "{$request->codigo}/check/CHECKOUT{$nombre}.png";
            Storage::disk('firma')->put($path, $image);

            DB::beginTransaction();

            if (!is_null(HCheckOut::where('codigo', $request->codigo)->first())) {
                throw new \Exception("La reservación con código {$request->codigo} ya cuenta con el check out registrado.", 1);
            };

            $distribucion_check = null;
            foreach ($request->check_out as $key => $check_out) {
                $check = HCheckOut::create(
                    [
                        'codigo' => $request->codigo,
                        'nombre' => $request->nombre,
                        'foto' => $path,
                        'lista' => $check_out['lista'],
                        'descripcion' => $request->descripcion,
                        'habitacion' => $check_out['habitacion'],
                        'h_reservaciones_id' => $check_out['h_reservaciones_id'],
                        'h_reservaciones_detalles_id' => $check_out['h_reservaciones_detalles_id'],
                        'usuarios_id' => Auth::user()->id,
                        'h_check_in_id' => $check_out['h_check_in_id'],
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

                    if ($agregado['checkout']) {
                        $kardex->stock_actual += $cantidad;
                        $kardex->activo = $kardex->stock_actual > 0 ? true : false;
                        $kardex->stock_consumido -= $cantidad;
                        $kardex->updated_at = date('Y-m-d H:i:s');
                        $kardex->save();
                    }

                    $this->historial_kardex("+ro", $kardex->stock_actual, $cantidad, $check->id, $kardex, $agregado['producto'], $check->getTable(), "Reservación {$check->codigo} - {$check->habitacion} (CheckOut)");
                }
            }

            HReservacion::where('id', $distribucion_check->h_reservaciones_id)->update(['check_out' => true]);
            HReservacionDetalle::where('h_reservaciones_id', $distribucion_check->h_reservaciones_id)->update(['disponible' => true]);

            DB::commit();

            return $this->successResponse(['mensaje' => "Check Out: se realizó el check out para la reservación con código {$request->codigo}."]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof ImageException) {
                return $this->errorResponse('Ocurrio un problema al grabar la firma del check out');
            } else if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al grabar la información del check out');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\V1\Hotel\HCheckOut  $check_out
     * @return \Illuminate\Http\Response
     */
    public function show(HCheckOut $check_out)
    {
        try {
            return $this->successResponse($check_out->detalle);
        } catch (\Exception $e) {
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HReservacion  $check_out
     * @return \Illuminate\Http\Response
     */
    public function destroy(HReservacion $check_out)
    {
        try {
            DB::beginTransaction();

            $checks = HCheckOut::where('h_reservaciones_id', $check_out->id)->get();

            $foto = null;
            foreach ($checks as $item) {
                foreach ($item->lista as $agregado) {
                    $kardex = HKardex::find($agregado['id']);

                    if (is_null($kardex)) {
                        throw new \Exception("El producto {$agregado['producto']} no esta registrado y no cuenta con inventario registrado.", 1);
                    }

                    $cantidad = intval($agregado['cantidad']);

                    if ($agregado['checkout']) {
                        $kardex->stock_actual -= $cantidad;
                        $kardex->activo = $kardex->stock_actual > 0 ? true : false;
                        $kardex->stock_consumido += $cantidad;
                        $kardex->updated_at = date('Y-m-d H:i:s');
                        $kardex->save();

                        $this->historial_kardex("aco", $kardex->stock_actual, $cantidad, $item->h_reservaciones_id, $kardex, $agregado['producto'], $item->getTable(), "Reservación {$item->codigo} - {$item->habitacion} (CheckOut)", true);
                    }
                }

                $item->delete();
                $foto = $item->foto;
            }

            $check_out->check_out = false;
            $check_out->save();

            HReservacionDetalle::where('h_reservaciones_id', $check_out->id)->update(['disponible' => false]);

            Storage::disk('firma')->exists($foto) ? Storage::disk('firma')->delete($foto) : null;

            DB::commit();

            return $this->successResponse("Check out: se realizó la anulación del check out para la reservación con código {$check_out->codigo}.");
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
