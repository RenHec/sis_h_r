<?php

namespace App\Http\Controllers\V1\Restaurante\Inventario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\V1\Restaurante\Inventario;

class InventarioController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columna    = $request['sortBy'] ? $request['sortBy'] : "nombre";
        $criterio   = $request['search'];
        $orden      = $request['sortDesc'] ? 'desc' : 'asc';
        $filas      = $request['perPage'];
        $pagina     = $request['page'];

        $inventario = DB::table('r_inventario as i')
                            ->join('r_producto as p','i.producto_id','p.id')
                            ->select('i.id','p.nombre','i.stock','i.suministrado','i.consumido')
                            ->where($columna, 'LIKE', '%' . $criterio . '%')
                            ->orderBy($columna, $orden)
                            ->skip($pagina)
                            ->take($filas)
                            ->get();

        $count            = DB::table('r_inventario as i')
                            ->join('r_producto as p','i.producto_id','p.id')
                            ->where($columna, 'LIKE', '%' . $criterio . '%')
                            ->count();

        $data             = array(
                                'total' => $count,
                                'data' => $inventario,
                            );

        return response()->json($data, 200);
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

            'productoId'    => 'required|numeric',
            'cantidad'      => 'required|numeric|min:1'
        ];

        $this->validate($request, $rules);

        $cantidad = $request->get('cantidad');

        $registro               = Inventario::where('producto_id',$request->get('productoId'))->first();
        $registro->producto_id  = $request->get('productoId');
        $registro->suministrado = $registro->suministrado + $cantidad;
        $registro->stock        = ((int)$cantidad + $registro->stock);
        $registro->save();

        return $this->showMessage('',201);
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
}
