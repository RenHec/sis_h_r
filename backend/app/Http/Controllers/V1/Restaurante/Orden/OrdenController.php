<?php

namespace App\Http\Controllers\V1\Restaurante\Orden;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\V1\Restaurante\Orden;
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
        $finaliza = DB::table('r_estado_orden')
                        ->select('id')
                        ->where('finaliza', 1)
                        ->first();

        $registros = DB::table('r_orden as o')
                        ->join('r_tipo_orden as to','o.tipo_orden_id','to.id')
                        ->join('r_estado_orden as eo','o.estado_orden_id','eo.id')
                        ->select('o.id','o.monto','o.fecha','o.hora','to.nombre as tipo_orden','eo.nombre as estado_orden','eo.icono')
                        ->where('eo.id','<>',$finaliza->id)
                        ->get();
        $datos = array();

        foreach ($registros as $key => $value) {

            $detalle = DB::table('r_orden_producto as op')
                        ->join('r_producto as p','op.producto_id','p.id')
                        ->select('op.id','op.cantidad','op.notas','p.nombre as producto','p.img','op.precio')
                        ->where('op.orden_id',$value->id)
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
            'detalle'       => 'required|array'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){

            $estado = EstadoOrden::select('id')
                        ->where('inicia',1)
                        ->first();

            $registro                   = new Orden();
            $registro->id               = $request->get('id');
            $registro->monto            = $request->get('monto');
            $registro->estado_orden_id  = $request->get('orden');
            $registro->tipo_orden_id    = $request->get('tipo_orden_id');
            $registro->fecha            = $request->get('fecha');
            $registro->hora             = $request->get('hora');
            $registro->usuario_id       = $request->user()->id;
            $registro->estado_orden_id  = $estado->id;
            $registro->save();

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function orderList(Request $request)
    {
        $columna    = $request['sortBy'] ? $request['sortBy'] : "monto";
        $criterio   = $request['search'];
        $orden      = $request['sortDesc'] ? 'desc' : 'asc';
        $filas      = $request['perPage'];
        $pagina     = $request['page'];

        $ordenes    = DB::table('r_orden as o')
                            ->join('r_estado_orden as eo','eo.id','o.estado_orden_id')
                            ->join('r_tipo_orden as to','to.id','o.tipo_orden_id')
                            ->select('o.id','o.monto','o.fecha','o.hora','eo.nombre as estado_orden','to.nombre as tipo_orden','eo.color','eo.icono')
                            ->where($columna, 'LIKE', '%' . $criterio . '%')
                            ->orderBy($columna, $orden)
                            ->skip($pagina)
                            ->take($filas)
                            ->get();

        $count      = DB::table('r_orden as o')
                        ->join('r_estado_orden as eo','eo.id','o.estado_orden_id')
                        ->join('r_tipo_orden as to','to.id','o.tipo_orden_id')
                        ->where($columna, 'LIKE', '%' . $criterio . '%')
                        ->count();

        $data       = array(
                        'total' => $count,
                        'data' => $ordenes,
                    );

        return response()->json($data, 200);
    }
}
