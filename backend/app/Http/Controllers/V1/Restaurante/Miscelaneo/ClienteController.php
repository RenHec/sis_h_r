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
        $rules = [
            'nit'           => 'required',
            'nombre'        => 'required',
            'telefono'      => 'nullable|numeric',
            'correo'        => 'nullable|email',
            'direccion'     => 'required',
            'departamento'  => 'required|numeric',
            'municipio'     => 'required|numeric'
        ];

        $this->validate($request, $rules);

        $registro                   = new Cliente();
        $registro->nit              = $request->get('nit');
        $registro->nombre           = $request->get('nombre');
        $registro->telefonos        = $request->get('telefono');
        $registro->emails           = $request->get('correo');
        $registro->direcciones      = $request->get('direccion');
        $registro->departamentos_id = $request->get('departamento');
        $registro->municipios_id    = $request->get('municipio');
        $registro->usuarios_id      = $request->user()->id;
        $registro->save();

        return $this->showMessage('', 201);
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
