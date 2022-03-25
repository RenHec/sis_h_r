<?php

namespace App\Http\Controllers\V1\Restaurante\Miscelaneo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\V1\Catalogo\Municipio;
use App\Http\Controllers\ApiController;

class MunicipioController extends ApiController
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $registros = DB::table('municipios')
                        ->select('id','nombre')
                        ->where('departamento_id',$id)
                        ->orderBy('nombre','asc')
                        ->get();

        return response()->json(['data'=>$registros],200);
    }
}
