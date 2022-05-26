<?php

namespace App\Http\Controllers\V1\Restaurante\Miscelaneo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\V1\Principal\Cliente;
use App\Http\Controllers\ApiController;

class ClienteController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            /*
                $persona->nit = $request->nit;
                $persona->nombre = $request->nombre;
                $persona->telefonos = $request->telefonos;
                $persona->emails = $request->emails;
                $persona->direcciones = $request->direcciones;
                $persona->municipios_id = $request->municipios_id['id];
                $persona->usuarios_id = Auth::user()->id;            
            */

            $this->cliente_proveedorR($request);

            return $this->showMessage('', 201);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $clientes = DB::table('clientes')
            ->select('id', 'nombre', 'nit', 'direcciones')
            ->where('nit', 'LIKE', '%' . $id . '%')
            ->get();

        return response()->json(['data' => $clientes], 200);
    }
}
