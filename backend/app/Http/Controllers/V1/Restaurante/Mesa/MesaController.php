<?php

namespace App\Http\Controllers\V1\Restaurante\Mesa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Restaurante\Mesa;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class MesaController extends ApiController
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

        $categoriasComida = DB::table('r_mesa')
                            ->select('id','nombre','icono')
                            ->where($columna, 'LIKE', '%' . $criterio . '%')
                            ->orderBy($columna, $orden)
                            ->skip($pagina)
                            ->take($filas)
                            ->get();

        $count            = DB::table('r_mesa')
                            ->where($columna, 'LIKE', '%' . $criterio . '%')
                            ->count();

        $data             = array(
                                'total' => $count,
                                'data' => $categoriasComida,
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

            'nombre'    => 'required|string',
            'icono'     => 'required|string'
        ];

        $this->validate($request, $rules);

        $registro           = new Mesa();
        $registro->nombre   = $request->get('nombre');
        $registro->icono    = $request->get('icono');
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
        $rules = [
            'nombre' => 'required|string',
            'icono' => 'required|string'
        ];

        $this->validate($request, $rules);

        $registro           = Mesa::findOrFail($id);
        $registro->nombre   = $request->get('nombre');
        $registro->icono    = $request->get('icono');
        $registro->save();

        return $this->showMessage('',202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $registro = Mesa::findOrFail($id);
        $registro->delete();

        return $this->showMessage('',210);
    }

    public function listTables()
    {
        $tables = Mesa::select('id','nombre','icono')
                        ->get();

        return response()->json(['data' => $tables],200);
    }
}
