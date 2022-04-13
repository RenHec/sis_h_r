<?php

namespace App\Http\Controllers\V1\Hotel\Habitacion;

use Illuminate\Http\Request;
use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;

class TipoCamaController extends ApiController
{
    private $controlador_principal = 'TipoCamaController';

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse(HTipoCama::orderByDesc('id')->get());
        } catch (\Throwable $e) {
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
            $data = HTipoCama::create($request->all());
            $this->bitacora_general($data->getTable(), $this->acciones(0), $data, "{$this->controlador_principal}@store");
            DB::commit();
            return $this->successResponse("Tipo de cama: {$data->nombre} fue registrado.");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información del tipo de cama');
            }

            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HTipoCama  $tipo_cama
     * @return \Illuminate\Http\Response
     */
    public function destroy(HTipoCama $tipo_cama)
    {
        try {
            DB::beginTransaction();
            $tipo_cama->delete();
            $this->bitacora_general($tipo_cama->getTable(), $this->acciones(2), $tipo_cama, "{$this->controlador_principal}@destroy");
            DB::commit();
            return $this->successResponse("Tipo de cama: {$tipo_cama->nombre} fue eliminado.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al eliminar la información del tipo de cama');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }
}
