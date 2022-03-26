<?php

namespace App\Http\Controllers\V1\Restaurante\Orden;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\V1\Restaurante\Orden;
use App\Models\V1\Restaurante\Venta;
use App\Http\Controllers\ApiController;
use App\Models\V1\Restaurante\EstadoOrden;
use App\Models\V1\Restaurante\OrdenProducto;

class OrdenController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $finaliza = EstadoOrden::select('id')
                                ->where('finaliza',1)
                                ->first();

        $registros = DB::table('r_orden as o')
                        ->join('r_tipo_orden as to','o.tipo_orden_id','to.id')
                        ->join('r_estado_orden as eo','o.estado_orden_id','eo.id')
                        ->join('r_mesa as m','o.mesa_id','m.id')
                        ->select('o.id','o.monto','o.fecha','o.hora','to.nombre as tipo_orden','eo.nombre as estado_orden','eo.id as estado_orden_id','eo.icono','m.nombre as mesa','eo.finaliza','eo.color')
                        ->where('eo.id','<>',$finaliza->id)
                        ->get();

        $datos = array();

        foreach ($registros as $key => $value) {

            $detalle = DB::table('r_orden_producto as op')
                        ->join('r_producto as p','op.producto_id','p.id')
                        ->select('op.id','op.cantidad','op.notas','p.nombre as producto','p.img','op.precio')
                        ->where('op.orden_id',$value->id)
                        ->where('op.activo',1)
                        ->get();

            $datos[$key]            = (array)$value;
            $datos[$key]['detalle'] = $detalle;
        }

        return response()->json(['data' => $datos],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'id'            => 'required',
            'monto'         => 'required|numeric|min:1',
            'tipo_orden_id' => 'required|numeric',
            'fecha'         => 'required|date',
            'hora'          => 'required',
            'detalle'       => 'required|array',
            'mesa_id'       => 'required|numeric|min:1'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){

            $estado = EstadoOrden::select('id')
                        ->where('inicia',1)
                        ->first();

            $registro = $this->ensureExistsOrderActive($request->get('mesa_id'));

            if($registro){
                $registro->monto            += $request->get('monto');
                $registro->estado_orden_id  = $estado->id;
                $registro->save();
            }else{
                $registro                   = new Orden();
                $registro->id               = $request->get('id');
                $registro->monto            = $request->get('monto');
                $registro->tipo_orden_id    = $request->get('tipo_orden_id');
                $registro->fecha            = $request->get('fecha');
                $registro->hora             = $request->get('hora');
                $registro->mesa_id          = $request->get('mesa_id');
                $registro->usuario_id       = $request->user()->id;
                $registro->estado_orden_id  = $estado->id;
                $registro->save();
            }

            foreach ($request->get('detalle') as $value) {
                OrdenProducto::create([
                    'cantidad'      => $value['cantidad'],
                    'orden_id'      => $registro->id,
                    'producto_id'   => $value['id'],
                    'precio'        => $value['precio']
                ]);
            }

            return $this->showMessage('',201);
        });
    }

    public function ensureExistsOrderActive($id)
    {
        $orden = Orden::select('id','monto')
                        ->where('mesa_id',$id)
                        ->where('activo',1)
                        ->first();

        if(!$orden){
            return null;
        }

        return $orden;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $registros = DB::table('r_orden as o')
                        ->join('r_tipo_orden as to','o.tipo_orden_id','to.id')
                        ->join('r_estado_orden as eo','o.estado_orden_id','eo.id')
                        ->join('r_mesa as m','o.mesa_id','m.id')
                        ->select('o.id','o.monto','o.fecha','o.hora','to.nombre as tipo_orden','eo.nombre as estado_orden','eo.id as estado_orden_id','eo.icono','m.nombre as mesa','eo.finaliza','eo.color')
                        ->where('o.id',$id)
                        ->where('o.activo',1)
                        ->get();
        $datos = array();

        foreach ($registros as $key => $value) {

            $detalle = DB::table('r_orden_producto as op')
                        ->join('r_producto as p','op.producto_id','p.id')
                        ->select('op.id','op.cantidad','op.notas','p.nombre as producto','p.img','op.precio')
                        ->where('op.orden_id',$value->id)
                        ->get();

            $datos            = (array)$value;
            $datos['detalle'] = $detalle;
        }

        return response()->json(['data' => $datos],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function orderList()
    {
        $ordenes    = DB::table('r_orden as o')
                            ->join('r_estado_orden as eo','eo.id','o.estado_orden_id')
                            ->join('r_tipo_orden as to','to.id','o.tipo_orden_id')
                            ->join('r_mesa as m','o.mesa_id','m.id')
                            ->select('o.id','o.monto','o.fecha','o.hora','eo.nombre as estado_orden','to.nombre as tipo_orden','eo.color','eo.icono','eo.finaliza','m.nombre as mesa')
                            ->where('o.activo',1)
                            ->get();

        return response()->json(['data' => $ordenes], 200);
    }

    public function updateOrderStatus(Request $request)
    {
        $rules = [
            'orden'            => 'required',
            'estado'           => 'required'
        ];

        $this->validate($request, $rules);

        if($this->verifyIfFinishedStatus($request->get('estado'))){
            $this->updateProductStatus($request->get('orden'));
        }

        $registro = Orden::findOrFail($request->get('orden'));
        $registro->estado_orden_id = $request->get('estado');
        $registro->save();

        return $this->showMessage('',204);
    }

    public function updateProductStatus($id)
    {
        $detalle =  DB::table('r_orden_producto')
                        ->where('orden_id', $id)
                        ->update(['activo' => 0]);

        if(!$detalle){
            throw new \Exception("Error al actualizar estado de los productos");
        }

        return;
    }

    public function verifyIfFinishedStatus($id)
    {
        $registro = EstadoOrden::findOrFail($id);

        return $registro->finaliza == 1;
    }

    public function orderPayment(Request $request)
    {
        $rules = [
            'id'            => 'required',
            'orden_id'      => 'required',
            'tipo_pago_id'  => 'required',
            'cliente_id'    => 'required',
            'monto'         => 'required'
        ];

        $this->validate($request, $rules);

        if(!$this->ensureHasNotOrderInKitchen($request->get('orden_id')))
        {
            return $this->errorResponse('La orden tiene productos pendientes de despacharse',);
        }

        return DB::transaction(function() use($request){
            $registro = new Venta();
            $registro->id = $request->get('id');
            $registro->orden_id = $request->get('orden_id');
            $registro->tipo_pago_id = $request->get('tipo_pago_id');
            $registro->cliente_id = $request->get('cliente_id');
            $registro->usuario_id = $request->user()->id;
            $registro->monto = $request->get('monto');
            $registro->save();

            $orden = Orden::findOrFail($request->get('orden_id'));
            $orden->activo = 0;
            $orden->save();

            return $this->showMessage('',201);
        });
    }

    public function ensureHasNotOrderInKitchen($ordenId)
    {
        $finaliza = EstadoOrden::select('id')
                                ->where('finaliza',1)
                                ->first();

        $orden = Orden::findOrFail($ordenId);

        return $orden->estado_orden_id == $finaliza->id;
    }
}
