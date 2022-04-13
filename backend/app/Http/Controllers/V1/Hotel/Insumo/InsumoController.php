<?php

namespace App\Http\Controllers\V1\Hotel\Insumo;

use Illuminate\Http\Request;
use App\Models\V1\Hotel\HInsumo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Models\V1\Hotel\HInsumoDetalle;
use App\Models\V1\Hotel\HKardex;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class InsumoController extends ApiController
{
    private $controlador_principal = 'InsumoController';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $no_anulados = HInsumo::with('proveedor.municipio', 'detalle', 'usuario')->where('anulado', false)->orderByDesc('id')->get();
            $anulados = HInsumo::with('proveedor', 'detalle', 'usuario')->where('anulado', true)->orderByDesc('id')->get();
            return $this->successResponse(['no_anulados' => $no_anulados, 'anulados' => $anulados]);
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

            /*
                $persona->nit = $request->nit;
                $persona->nombre = $request->nombre;
                $persona->telefonos = $request->telefonos;
                $persona->emails = $request->emails;
                $persona->direcciones = $request->direcciones;
                $persona->municipios_id = $request->municipios_id['id];
                $persona->usuarios_id = Auth::user()->id;            
            */
            $proveedor = $this->cliente_proveedor($request, false);

            $insumo = HInsumo::create(
                [
                    'documento' => $request->documento,
                    'sub_total' => 0,
                    'descuento' => 0,
                    'total' => 0,
                    'anulado' => false,
                    'nombre_proveedor' => $proveedor->nombre,
                    'proveedores_id' => $proveedor->id,
                    'usuarios_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $cantidad_registrados = 0;
            foreach ($request->h_insumos_detalles as $key => $value) {
                $kardex = HKardex::find($value['h_productos_id']['id']); // h_productos_id, en este valor viene el ID del kardex

                if (is_null($kardex)) {
                    throw new \Exception("El producto {$value['h_productos_id']['producto']} no esta registrado y no cuenta con inventario registrado.", 1);
                }

                if (intval($value['cantidad']) < 1) {
                    throw new \Exception("El producto {$value['h_productos_id']['producto']} cuenta con la cantidad inválida de {$value['cantidad']}.", 1);
                }

                $cantidad = intval($value['cantidad']);
                $precio = floatval($value['precio']);
                $descuento = floatval($value['descuento']);
                $sub_total = ($cantidad * $precio) - $descuento;

                $detalle = HInsumoDetalle::create(
                    [
                        'documento' => $insumo->documento,
                        'producto' => $value['h_productos_id']['producto'],
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'descuento' => $descuento,
                        'sub_total' => $sub_total,
                        'h_insumos_id' => $insumo->id,
                        'h_productos_id' => $kardex->h_productos_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );

                $this->bitacora_general($detalle->getTable(), $this->acciones(0), $detalle, "{$this->controlador_principal}@store");

                $kardex->stock_inicial = $kardex->stock_inicial > 0 ? $kardex->stock_inicial : $cantidad;
                $kardex->stock_actual += $cantidad;
                $kardex->activo = $kardex->stock_actual > 0 ? true : false;
                $kardex->updated_at = date('Y-m-d H:i:s');
                $kardex->save();

                $this->bitacora_general($kardex->getTable(), $this->acciones(1), $kardex, "{$this->controlador_principal}@store");

                $this->historial_kardex("+i", $kardex->stock_actual, $cantidad, $detalle->id, $kardex, $detalle->producto, $insumo->getTable(), "Insumo - {$insumo->documento}");

                $insumo->sub_total += ($cantidad * $precio);
                $insumo->descuento += $detalle->descuento;
                $insumo->total = $insumo->sub_total - $insumo->descuento;
                $insumo->save();

                $cantidad_registrados += 1;
            }

            $this->bitacora_general($insumo->getTable(), $this->acciones(0), $insumo, "{$this->controlador_principal}@store");
            $this->registrar_historia_caja("Movimiento de insumo número {$insumo->documento}", $insumo->total, "NA", "0", "{$this->controlador_principal}@store");

            DB::commit();
            return $this->successResponse("Insumo: el documento {$insumo->documento} fue registrado con un total de {$cantidad_registrados} registros y un total de Q{$insumo->total}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al grabar la información de la compra de insumo');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HInsumo  $insumo
     * @return \Illuminate\Http\Response
     */
    public function destroy(HInsumo $insumo)
    {
        try {
            DB::beginTransaction();

            $detalle = HInsumoDetalle::where('h_insumos_id', $insumo->id)->get();

            $cantidad_anulados = 0;
            foreach ($detalle as $value) {
                $kardex = HKardex::where('h_productos_id', $value->h_productos_id)->firts();

                if (($kardex->stock_actual - $value->cantidad) < 0) {
                    throw new \Exception("El producto {$kardex->producto->nombre} no tiene el stock suficiente para la anulación del documento {$insumo->documento}.", 1);
                }

                $kardex->stock_actual -= $value->cantidad;
                $kardex->activo = $kardex->stock_actual > 0 ? true : false;
                $kardex->save();

                $this->bitacora_general($kardex->getTable(), $this->acciones(1), $kardex, "{$this->controlador_principal}@destroy");

                $this->historial_kardex("ai", $kardex->stock_actual, $value->cantidad, $detalle->id, $kardex, $kardex->producto->nombre, $insumo->getTable(), "Insumo - {$insumo->documento}", true);

                $cantidad_anulados += 1;
            }

            $insumo->anulado = true;
            $insumo->save();

            $this->bitacora_general($insumo->getTable(), $this->acciones(1), $insumo, "{$this->controlador_principal}@destroy");
            $this->registrar_historia_caja("Anulación de movimiento de insumo número {$insumo->documento}", $insumo->total, "NA", "0", "{$this->controlador_principal}@destroy");

            DB::commit();
            return $this->successResponse("Insumo: el documento {$insumo->documento} fue anulado con un total de {$cantidad_anulados} registros.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al grabar la información de la compra de insumo');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }
}
