<?php

namespace App\Http\Controllers\V1\Hotel\Pago;

use Illuminate\Http\Request;
use App\Models\V1\Hotel\HPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Hotel\HReservacion;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class PagoController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse(HPago::with('reservacion', 'tipo_pago')->get());
        } catch (\Exception $e) {
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

            HReservacion::where('id', $pago->h_reservaciones_id)->update(['pagado' => true]);

            $ticket = $this->generarTicket($pago, null);
            $path = "/hotel/{$pago->correlativo}.pdf";
            Storage::disk('ticket')->put($path, $ticket);

            $pago->path = $path;
            $pago->save();

            DB::commit();
            return $this->successResponse(['mensaje' => "Pago: se genero el pago de la reservación {$request->codigo} con el número de pago {$pago->correlativo}.", 'pago' => $pago->getTicketAttribute()]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información del pago');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse($e->getMessage());
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
            DB::beginTransaction();

            Storage::disk('ticket')->exists($pago->path) ? Storage::disk('ticket')->delete($pago->path) : null;
            $pago->delete();

            DB::commit();

            return $this->successResponse("Check out: se realizó la anulación del check out para la reservación con código {$check_out->codigo}.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al anular la información del check in.');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }
}
