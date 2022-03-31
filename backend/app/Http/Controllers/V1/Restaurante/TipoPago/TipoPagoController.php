<?php

namespace App\Http\Controllers\V1\Restaurante\TipoPago;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\V1\Catalogo\TipoPago;
use App\Http\Controllers\ApiController;

class TipoPagoController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $registros = TipoPago::select('id','nombre','ticket')->get();

        return $this->showAll($registros);
    }
}
