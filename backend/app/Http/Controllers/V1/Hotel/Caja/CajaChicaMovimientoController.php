<?php

namespace App\Http\Controllers\V1\Hotel\Caja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Hotel\HCajaChica;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use App\Models\V1\Hotel\HCajaChicaMovimiento;

class CajaChicaMovimientoController extends ApiController
{
    private $controlador_principal = 'CajaChicaMovimientoController';

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Hotel\HCajaChicaMovimiento  $hotel_caja_movimiento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HCajaChica $hotel_caja_movimiento)
    {
        try {
            DB::beginTransaction();

            $hoy = date('Y-m-d');
            $caja = HCajaChica::where('id', $hotel_caja_movimiento->id)->whereDate('inicio', $hoy)->where('abierta', true)->first();

            if (is_null($caja)) {
                throw new \Exception("NO existe apertura de caja para hoy {$hoy}.", 1);
            }

            $this->registrar_historia_caja($request->descripcion, $request->monto_total, $request->tipo_pago, $request->comprobante, "{$this->controlador_principal}@update");

            DB::commit();
            return $this->successResponse("Movimiento caja: Movimiento de caja chica fue registrada con Q{$request->monto_total}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información del movimiento de la caja');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HCajaChicaMovimiento  $hotel_caja_movimiento
     * @return \Illuminate\Http\Response
     */
    public function destroy(HCajaChicaMovimiento $hotel_caja_movimiento)
    {
        try {
            DB::beginTransaction();

            $hoy = date('Y-m-d');
            $caja = HCajaChica::where('id', $hotel_caja_movimiento->h_caja_chica_id)->whereDate('inicio', $hoy)->where('abierta', true)->first();

            if (is_null($caja)) {
                throw new \Exception("NO existe apertura de caja para hoy {$hoy}.", 1);
            }

            $this->registrar_historia_caja("Anulación de movimiento de compra", $hotel_caja_movimiento->monto_total, $hotel_caja_movimiento->tipo_pago, $hotel_caja_movimiento->comprobante, "{$this->controlador_principal}@destroy");

            DB::commit();
            return $this->successResponse("Movimiento caja: Movimiento de caja chica fue registrada con Q{$hotel_caja_movimiento->monto_total}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información del movimiento de la caja');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }
}
