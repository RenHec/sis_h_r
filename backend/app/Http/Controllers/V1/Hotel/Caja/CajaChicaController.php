<?php

namespace App\Http\Controllers\V1\Hotel\Caja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Hotel\HCajaChica;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use App\Models\V1\Hotel\HCajaChicaMovimiento;

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
            return $this->successResponse(HCajaChica::with('movimientos.usuario', 'usuario', 'mes')->orderByDesc('id')->get());
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@index");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $hoy = date('Y-m-d');
            $caja = HCajaChica::with('movimientos', 'usuario')->whereDate('inicio', $hoy)->where('abierta', true)->get();

            if (count($caja) == 0) {
                throw new \Exception("Es necesario que aperture la caja para poder operar, insumos, compras, reservaciones, etc..., en el sistema.", 1);
            }

            return $this->successResponse($caja[0]);
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@index");
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
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

            $hoy = date('Y-m-d');
            $caja = HCajaChica::whereDate('inicio', $hoy)->where('abierta', true)->first();

            if (!is_null($caja)) {
                throw new \Exception("Ya cuenta con la apertura de caja.", 1);
            }

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

            $formato = number_format($caja_chica->inicia_caja, 2);
            return $this->successResponse("Caja chica: Apertura de caja chica fue registrada con Q {$formato}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información de la caja chica');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
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
            $hotel_caja->cierre_caja = ($hotel_caja->inicia_caja + $hotel_caja->pagos + $hotel_caja->restaurante) - ($hotel_caja->insumos + $hotel_caja->compras);
            $hotel_caja->abierta = false;
            $hotel_caja->save();

            $this->bitacora_general($hotel_caja->getTable(), $this->acciones(8), $hotel_caja, "{$this->controlador_principal}@show");

            HCajaChicaMovimiento::create([
                'descripcion' => "Cierre de la caja #{$hotel_caja->id}",
                'monto_total' => $hotel_caja->cierre_caja,
                'tipo_pago' => "EFECTIVO",
                'comprobante' => null,
                'usuarios_id' => Auth::user()->id,
                'h_caja_chica_id' => $hotel_caja->id,
                'resta' => false,
                'registro_manual' => false,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            DB::commit();

            $formato = number_format($hotel_caja->cierre_caja, 2);
            return $this->successResponse("Caja chica: Cierre de caja chica fue registrada con Q {$formato}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información de la caja chica');
            }
            return $this->errorResponse($e->getMessage());
        }
    }
}
