<?php

namespace App\Http\Controllers\V1\Compartido\Catalogo;

use App\Models\V1\Catalogo\Mes;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Catalogo\Municipio;
use App\Http\Controllers\ApiController;
use App\Models\V1\Catalogo\Departamento;
use App\Models\V1\Catalogo\Presentacion;
use App\Models\V1\Hotel\HTipoCama;
use App\Models\V1\Principal\Cliente;
use App\Models\V1\Principal\Proveedor;

class SelectController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function departamento()
    {
        return $this->showAll(Departamento::orderBy('nombre')->get());
    }

    public function municipio()
    {
        return $this->showAll(Municipio::orderBy('departamento_id')->get());
    }

    public function presentacion()
    {
        return $this->showAll(Presentacion::whereNull('deleted_at')->orderBy('nombre')->get());
    }

    public function cliente()
    {
        return $this->showAll(Cliente::with('municipios')->whereNull('deleted_at')->orderBy('nombre')->get());
    }

    public function proveedor()
    {
        return $this->showAll(Proveedor::whereNull('deleted_at')->orderBy('nombre')->get());
    }

    public function mes()
    {
        return $this->showAll(Mes::orderBy('id')->get());
    }

    public function tipo_pago()
    {
        return $this->showAll(TipoPago::orderBy('nombre')->get());
    }

    public function tipo_cama()
    {
        return $this->showAll(HTipoCama::orderBy('nombre')->get());
    }
}
