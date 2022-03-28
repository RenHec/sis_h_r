<?php

namespace App\Http\Controllers\V1\Restaurante\Orden;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\V1\Restaurante\Orden;
use App\Http\Controllers\ApiController;
use App\Models\V1\Restaurante\OrdenProducto;

class OrdenDetailController extends ApiController
{
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
