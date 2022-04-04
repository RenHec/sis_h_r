<?php

namespace App\Http\Controllers\V1\Hotel\Pago;

use Illuminate\Http\Request;
use App\Models\V1\Hotel\HPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Hotel\HReservacion;
use App\Http\Controllers\ApiController;
use App\Models\V1\Catalogo\TipoPago;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HReservacionDetalle;

class PagoController extends ApiController
{
    private $controlador_principal = 'PagoController';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $pagos = HPago::with('reservacion', 'tipo_pago')->where('anulado', false)->orderBy('created_at')->get();
            $anulados = HPago::with('reservacion', 'tipo_pago')->where('anulado', true)->orderBy('created_at')->get();
            return $this->successResponse(['pagos' => $pagos, 'anulados' => $anulados]);
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
            DB::beginTransaction();

            $pago = HPago::create(
                [
                    'correlativo' => $this->generadorCodigo("", HPago::whereYear('created_at', date('Y'))->count()),
                    'nit' => $request->nit,
                    'nombre' => $request->nombre,
                    'ubicacion' => str_replace(", null", "", $request->direccion),
                    'vaucher_pago' => $request->vaucher_pago,
                    'detalle' => $request->detalle,
                    'factura' => $request->factura,
                    'sub_total' => $request->sub_total,
                    'descuento' => $request->descuento,
                    'total' => $request->sub_total - $request->descuento,
                    'consumo_restaurante' => 0,
                    'h_reservaciones_id' => $request->id,
                    'tipos_pagos_id' => $request->tipos_pagos_id,
                    'usuarios_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $reservacion = HReservacion::where('id', $pago->h_reservaciones_id)->update(['pagado' => true]);

            $this->bitacora_general($reservacion->getTable(), $this->acciones(3), $reservacion, "{$this->controlador_principal}@store");

            $ticket = $this->generarTicket($pago, null);
            $path = "/hotel/{$pago->correlativo}.pdf";
            Storage::disk('ticket')->put($path, $ticket);

            $pago->path = $path;
            $pago->consumo_restaurante = $reservacion->extra;
            $pago->save();

            $this->bitacora_general($pago->getTable(), $this->acciones(0), $pago, "{$this->controlador_principal}@store");
            $this->registrar_historia_caja("Movimiento de reservación pagada comprobante {$pago->correlativo}", $pago->total, TipoPago::find($pago->tipos_pagos_id)->nombre, $pago->vaucher_pago, "{$this->controlador_principal}@store", $pago->consumo_restaurante);

            DB::commit();
            return $this->successResponse(['mensaje' => "Pago: se genero el pago de la reservación {$request->codigo} con el número de pago {$pago->correlativo}.", 'pago' => $pago->getTicketAttribute()]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información del pago');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\V1\Hotel\HPago  $pago
     * @return \Illuminate\Http\Response
     */
    public function show(HPago $pago)
    {
        try {
            return $this->successResponse($pago->getTicketAttribute());
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@show");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HPago  $pago
     * @return \Illuminate\Http\Response
     */
    public function destroy(HPago $pago)
    {
        try {
            $fecha_hoy = date('d/m/Y');
            $created_at = date('d/m/Y', strtotime($pago->created_at));

            if (strtotime($created_at) != strtotime($fecha_hoy)) {
                throw new \Exception("El pago con número de comprobante {$pago->correlativo} no puede ser anulado.", 1);
            }

            DB::beginTransaction();

            $reservacion = HReservacion::where('id', $pago->h_reservaciones_id)->update(['pagado' => false]);
            $this->bitacora_general($reservacion->getTable(), $this->acciones(3), $reservacion, "{$this->controlador_principal}@destroy");

            $detalle = HReservacionDetalle::where('h_reservaciones_id', $pago->h_reservaciones_id)->update(['disponible' => false]);
            $this->bitacora_general($detalle->getTable(), $this->acciones(3), $detalle, "{$this->controlador_principal}@destroy");

            Storage::disk('ticket')->exists($pago->path) ? Storage::disk('ticket')->delete($pago->path) : null;
            $pago->anulado = true;
            $pago->save();

            $this->bitacora_general($pago->getTable(), $this->acciones(3), $pago, "{$this->controlador_principal}@destroy");
            $this->registrar_historia_caja("Anulación de movimiento de reservación pagada comprobante {$pago->correlativo}", $pago->total, TipoPago::find($pago->tipos_pagos_id)->nombre, $pago->vaucher_pago, "{$this->controlador_principal}@destroy", $pago->consumo_restaurante);

            DB::commit();

            return $this->successResponse("Pago: se realizó la anulación del pago con número de comprobante {$pago->correlativo}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al anular la información del pago.');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }
}
