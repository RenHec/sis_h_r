<?php

namespace App\Http\Controllers\V1\Hotel\Insumo;

use Illuminate\Http\Request;
use App\Models\V1\Hotel\HKardex;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Models\V1\Hotel\HProducto;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class Kardex extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse(HKardex::with('historial', 'producto', 'usuario')->get());
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

            $producto = HProducto::create(
                ['nombre' => $request->nombre, 'descripcion' => $request->descripcion, 'consumible' => $request->consumible, 'activo' => true, 'usuarios_id' => Auth::user()->id]
            );

            $kardex = HKardex::create(
                [
                    'stock_actual' => $request->stock_inicial,
                    'stock_inicial' => $request->stock_inicial,
                    'stock_consumido' => 0,
                    'h_productos_id' => $producto->id,
                    'usuarios_id' => $producto->usuarios_id,
                    'activo' => $request->stock_inicial > 0 ? true : false,
                    'check_in' => $request->check_in
                ]
            );

            if ($kardex->stock_actual > 0) {
                $this->historial_kardex("=", $kardex->stock_actual, $kardex->stock_actual, 0, $kardex, $producto->nombre);
            }

            DB::commit();
            return $this->successResponse("Producto: {$producto->nombre} fue registrado y con un stock inicial de {$kardex->stock_actual}.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al grabar la información del kardex');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\V1\Hotel\HProducto  $kardex
     * @return \Illuminate\Http\Response
     */
    public function show(HProducto $kardex)
    {
        try {
            return $this->successResponse(["producto" => $kardex, "usuario" => $kardex->usuario, "kardex" => $kardex->kardex]);
        } catch (\Exception $e) {
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Hotel\HKardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HKardex $kardex)
    {
        try {
            DB::beginTransaction();

            $producto = HProducto::find($kardex->h_productos_id);
            $producto->nombre = $request->nombre;
            $producto->descripcion = $request->descripcion;
            $producto->consumible = $request->consumible;
            $producto->save();

            $kardex->check_in = $request->check_in;
            if ($request->stock_inicial > 0) {
                $kardex->stock_actual = $request->stock_inicial;
                $kardex->stock_inicial = $request->stock_inicial;
                $kardex->activo = $request->stock_inicial > 0 ? true : false;

                $this->historial_kardex("=", $kardex->stock_inicial, $kardex->stock_inicial, 0, $kardex, $producto->nombre);
            }
            $kardex->save();

            $this->historial_kardex("=", $kardex->stock_inicial, $kardex->stock_inicial, 0, $kardex, $producto->nombre);

            DB::commit();
            return $this->successResponse("Producto: {$producto->nombre} fue registrado y con un stock inicial de {$kardex->stock_actual}.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al grabar la información de la habitación');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HProducto  $kardex
     * @return \Illuminate\Http\Response
     */
    public function destroy(HProducto $kardex)
    {
        try {
            $kardex->activo = $kardex->activo ? false : true;
            $kardex->save();
            $mensaje = $kardex->activo ? 'ALTA' : 'BAJA';
            return $this->successResponse("El producto {$kardex->nombre} fue dado de {$mensaje}.");
        } catch (\Exception $e) {
            return $this->errorResponse('Error en el controlador');
        }
    }
}
