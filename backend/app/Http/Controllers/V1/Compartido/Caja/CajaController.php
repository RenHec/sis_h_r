<?php

namespace App\Http\Controllers\V1\Principal;

use Illuminate\Http\Request;
use App\Models\V1\Principal\Caja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use App\Models\V1\Principal\CajaPago;

class CajaController extends ApiController
{
    private $tabla_principal = 'cajas';
    private $controlador_principal = 'CajaController';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->showAll(Caja::with('usuarios', 'meses', 'empresas')->where('usuarios_id', '<>', 1)->whereIn('empresas_id', $this->empresas_asignadas())->get());
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

            if (Caja::where(['usuarios_id' => $request->usuarios_id['id'], 'abierta' => true])->exists()) {
                return $this->errorResponse("Actualmente el vendedor {$request->usuarios_id['full_name']} cuenta con una apertura de caja ABIERTA en la empresa {$request->empresas_id['nombre']}.");
            }

            $caja = Caja::create(
                [
                    'inicio' => date('Y-m-d H:i:s'),
                    'finalizo' => null,
                    'inicia_caja' => $request->inicia_caja,
                    'venta_total' => 0,
                    'compra_total' => 0,
                    'creditos' => 0,
                    'devolucion' => 0,
                    'abierta' => true,
                    'meses_id' => date('n'),
                    'usuarios_id' => $request->usuarios_id['id']
                ]
            );

            $this->bitacora_general($this->tabla_principal, $this->acciones(7), $caja, "{$this->controlador_principal}@store");

            DB::commit();

            $monto = number_format($caja->inicia_caja, 2, '.', ',');

            return $this->successResponse("Hola, {$request->usuarios_id['primer_nombre']} {$request->usuarios_id['primer_apellido']} hemos aparturado la caja con Q {$monto}, fecha de apertura {$caja->inicio}");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\V1\Principal\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function show(Caja $caja)
    {
        try {
            $registros = CajaPago::with('tipos_pagos')->where('cajas_id', $caja->id)->get();
            return $this->showAll($registros);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Principal\Caja  $bodega
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caja $caja)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $caja->finalizo = date('Y-m-d H:i:s');
            $caja->abierta = false;
            $caja->save();

            $this->bitacora_general($this->tabla_principal, $this->acciones(8), $caja, "{$this->controlador_principal}@destroy");

            DB::commit();

            return $this->successResponse("Hola, {$user->primer_nombre} {$user->primer_apellido} hemos cerrado la caja, fecha de cierre {$caja->finalizo}");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error en el controlador');
        }
    }
}
