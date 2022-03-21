<?php

namespace App\Http\Controllers\V1\Compartido\Catalogo;

use App\Models\V1\Catalogo\Mes;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Catalogo\Municipio;
use App\Http\Controllers\ApiController;
use App\Models\V1\Catalogo\Departamento;
use App\Models\V1\Catalogo\Presentacion;
use App\Models\V1\Hotel\HEstado;
use App\Models\V1\Hotel\HKardex;
use App\Models\V1\Hotel\HTipoCama;
use App\Models\V1\Principal\Cliente;
use App\Models\V1\Principal\Proveedor;
use Illuminate\Support\Facades\DB;

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
        return $this->showAll(Cliente::with('municipio')->whereNull('deleted_at')->orderBy('nombre')->get());
    }

    public function proveedor()
    {
        return $this->showAll(Proveedor::with('municipio')->whereNull('deleted_at')->orderBy('nombre')->get());
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
        return $this->showAll(HTipoCama::select('id', DB::RAW("CONCAT(nombre,' | Persona ',cantidad) AS nombre"))->orderBy('nombre')->get());
    }

    public function estado_habitacion()
    {
        return $this->showAll(HEstado::orderBy('nombre')->get());
    }

    public function producto_insumo()
    {
        $data = DB::table('h_productos')
            ->join('h_kardex', 'h_kardex.h_productos_id', 'h_productos.id')
            ->select(
                'h_kardex.id AS id',
                'h_productos.nombre AS producto',
                'h_kardex.stock_actual AS stock_actual'
            )
            ->where('h_productos.activo', true)
            ->where('h_kardex.activo', true)
            ->get();

        return $this->showAll($data);
    }

    public function producto_check_in()
    {
        $data = DB::table('h_productos')
            ->join('h_kardex', 'h_kardex.h_productos_id', 'h_productos.id')
            ->select(
                'h_kardex.id AS id',
                'h_productos.nombre AS producto',
                'h_kardex.stock_actual AS stock_actual'
            )
            ->where('h_productos.activo', true)
            ->where('h_kardex.activo', true)
            ->where('h_kardex.check_in', true)
            ->get();

        return $this->showAll($data);
    }
}
