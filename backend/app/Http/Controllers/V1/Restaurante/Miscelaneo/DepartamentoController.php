<?php

namespace App\Http\Controllers\V1\Restaurante\Miscelaneo;

use App\Http\Controllers\ApiController;
use App\Models\V1\Catalogo\Departamento;

class DepartamentoController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $registros = Departamento::select('id','nombre')
                            ->orderBy('nombre','asc')
                            ->get();

        return $this->showAll($registros, 200);
    }
}
