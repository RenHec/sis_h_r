<?php

namespace App\Http\Controllers\V1\Restaurante\Orden;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\V1\Restaurante\Orden;
use App\Http\Controllers\ApiController;
use App\Models\V1\Restaurante\EstadoOrden;
use App\Models\V1\Restaurante\OrdenProducto;
use App\Models\V1\Restaurante\Producto;

class OrdenDetailController extends ApiController
{
    public function modifyStateWaiterOrderDetail(Request $request)
    {
        $rules = [
            'orden'            => 'required'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){

            $detalle =  DB::table('r_orden_producto as op')
                        ->join('r_producto as p','op.producto_id','p.id')
                        ->where('op.orden_id', $request->get('orden'))
                        ->where('p.quien_prepara','<>',Producto::COCINA)
                        ->update(['op.activo' => 0]);

            //Verificar si existen productos activos, sino actualizar el estado final
            $activos = DB::table('r_orden_producto')
                    ->select('id')
                    ->where('orden_id',$request->get('orden'))
                    ->where('activo',1)
                    ->get();

            if(count($activos) < 1){

                $final = EstadoOrden::where('finaliza',1)->first();
                $registro = Orden::findOrFail($request->get('orden'));
                $registro->estado_orden_id = $final->id;
                $registro->save();
            }

            return $this->showMessage('',204);
        });
    }
    public function modifyStateAllOrderDetail(Request $request)
    {
        $rules = [
            'orden'            => 'required'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){

            $final = EstadoOrden::where('finaliza',1)->first();

            $detalle =  DB::table('r_orden_producto')
                            ->where('orden_id', $request->get('orden'))
                            ->update(['activo' => 0]);

            $registro = Orden::findOrFail($request->get('orden'));
            $registro->estado_orden_id = $final->id;
            $registro->save();

            return $this->showMessage('',204);
        });
    }
    public function getDetailOrder($orderId)
    {
        $details = DB::table('r_orden_producto as op')
                    ->join('r_producto as p','op.producto_id','p.id')
                    ->select('op.id','op.orden_id','op.cantidad','p.img','op.precio','p.nombre as producto')
                    ->where('op.activo',1)
                    ->where('op.orden_id',$orderId)
                    ->get();

        return response()->json(['data' => $details],200);
    }

    public function setMinusQuantity(Request $request)
    {
        $rules = [
            'detalle'   => 'required',
            'orden'     => 'required'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){
            $registro = OrdenProducto::findOrFail($request->get('detalle'));
            $registro->cantidad = $registro->cantidad-1;
            $registro->save();

            $orden = Orden::findOrFail($request->get('orden'));
            $orden->monto = ($registro->precio - $orden->monto);
            $orden->save();

            return $this->showMessage([],200);
        });
    }

    public function setPlusQuantity(Request $request)
    {
        $rules = [
            'detalle'   => 'required',
            'orden'     => 'required'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){
            $registro = OrdenProducto::findOrFail($request->get('detalle'));
            $registro->cantidad = $registro->cantidad+1;
            $registro->save();

            $orden = Orden::findOrFail($request->get('orden'));
            $orden->monto = ($registro->precio + $orden->monto);
            $orden->save();

            return $this->showMessage([],200);
        });
    }

    public function deleteOneDetailOrder(Request $request)
    {
        $rules = [
            'detalle'   => 'required',
            'orden'     => 'required'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){
            $detail = OrdenProducto::findOrFail($request->get('detalle'));
            $detail->delete();

            $order = Orden::findOrFail($request->get('orden'));
            $order->monto = $order->monto - ($detail->precio * $detail->cantidad);
            $order->save();

            return $this->showMessage([],200);
        });
    }

    public function deleteAllOrderDetail(Request $request){
        $rules = [
            'orden'     => 'required'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){
            $detail = OrdenProducto::where('orden_id',$request->get('orden'))
                        ->delete();

            $order = Orden::findOrFail($request->get('orden'));
            $order->delete();

            return $this->showMessage([],200);
        });
    }
}
