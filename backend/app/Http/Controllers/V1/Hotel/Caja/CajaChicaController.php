<?php

namespace App\Http\Controllers\V1\Hotel\Caja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Hotel\HCajaChica;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class CajaChicaController extends ApiController
{
    private $controlador_principal = 'CajaChicaController';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse(HCajaChica::with('movimientos', 'usuario')->get());
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

            $caja_chica = HCajaChica::create(
                [
                    'inicio' => date('Y-m-d H:i:s'),
                    'finalizo' => null,
                    'inicia_caja' => $request->inicia_caja,
                    'pagos' => 0,
                    'insumos' => 0,
                    'compras' => 0,
                    'restaurante' => 0,
                    'cierre_caja' => 0,
                    'anio' => date('Y'),
                    'meses_id' => date('n'),
                    'dia' => date('j'),
                    'abierta' => true,
                    'usuarios_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $this->bitacora_general($caja_chica->getTable(), $this->acciones(7), $caja_chica, "{$this->controlador_principal}@store");

            DB::commit();
            return $this->successResponse("Caja chica: Apertura de caja chica fue registrada con Q{$caja_chica->inicia_caja}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información de la caja chica');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Show the specified resource in storage.
     *
     * @param  \App\Models\V1\Hotel\HCajaChica  $hotel_caja
     * @return \Illuminate\Http\Response
     */
    public function show(HCajaChica $hotel_caja)
    {
        try {
            DB::beginTransaction();

            $hotel_caja->finalizo = date('Y-m-d H:i:s');
            $hotel_caja->cierre_caja = ($hotel_caja->inicia_caja + $hotel_caja->pagos) - ($hotel_caja->insumos + $hotel_caja->compras);
            $hotel_caja->abierta = false;
            $hotel_caja->save();

            $this->bitacora_general($hotel_caja->getTable(), $this->acciones(8), $hotel_caja, "{$this->controlador_principal}@update");

            DB::commit();
            return $this->successResponse("Caja chica: Cierre de caja chica fue registrada con Q{$hotel_caja->cierre_caja}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información de la caja chica');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }
}
