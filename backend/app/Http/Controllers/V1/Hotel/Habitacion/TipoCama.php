<?php

namespace App\Http\Controllers\V1\Hotel\Habitacion;

use Illuminate\Http\Request;
use App\Models\V1\Hotel\HTipoCama;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;

class TipoCama extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $columna    = $this->traInformacion($request['sortBy']) ? $request['sortBy'] : "nombre";
            $criterio   = $this->traInformacion($request['search']) ? $request['search'] : null;
            $orden      = $this->traInformacion($request['sortDesc']) ? $request['sortDesc'] : 'asc';
            $filas      = $request['perPage'];
            $pagina     = $request['page'];

            $consulta = HTipoCama::when(!is_null($criterio), function ($query) use ($columna, $criterio) {
                $query->where($columna, 'LIKE', "%{$criterio}%");
            })
                ->orderBy($columna, $orden)
                ->skip($pagina)
                ->take($filas)
                ->get();

            $data = array(
                'total' => count($consulta),
                'data' => $consulta,
            );

            return $this->successResponse($data);
        } catch (\Throwable $th) {
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
            $data = HTipoCama::create($request->all());
            return $this->successResponse("Tipo de cama: {$data->nombre} fue registrado.");
        } catch (\Throwable $th) {
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\V1\Hotel\HTipoCama  $tipo_cama
     * @return \Illuminate\Http\Response
     */
    public function show(HTipoCama $tipo_cama)
    {
        //
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
            $tipo_cama->delete();
            return $this->successResponse("Tipo de cama: {$tipo_cama->nombre} fue eliminado.");
        } catch (\Exception $e) {
            if ($e instanceof QueryException) {
                return $this->errorResponse('El registro se encuentra en uso');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }
}
