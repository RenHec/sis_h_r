<?php

namespace App\Http\Controllers\V1\Compartido\Catalogo;

use App\Models\V1\Catalogo\Mes;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Catalogo\Municipio;
use App\Http\Controllers\ApiController;
use App\Models\V1\Catalogo\Departamento;
use App\Models\V1\Catalogo\Presentacion;
use App\Models\V1\Hotel\HEstado;
use App\Models\V1\Hotel\HHabitacionFoto;
use Illuminate\Http\Request;
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

    public function precios($habitacion)
    {
        $data = DB::table('h_habitaciones_precios')
            ->join('h_habitaciones', 'h_habitaciones.id', 'h_habitaciones_precios.h_habitaciones_id')
            ->join('h_tipos_camas', 'h_tipos_camas.id', 'h_habitaciones_precios.h_tipos_camas_id')
            ->select(
                'h_habitaciones_precios.id AS id',
                'h_habitaciones_precios.precio AS precio',
                'h_tipos_camas.nombre AS nombre',
                'h_tipos_camas.cantidad AS cantidad',
                'h_habitaciones.numero AS habitacion',
                'h_habitaciones.id AS habitacion_id',
                DB::RAW("CONCAT('Habitación ',h_habitaciones.numero,' | Cama: ',h_tipos_camas.nombre) AS nombre_completo")
            )
            ->where('h_habitaciones_precios.activo', true)
            ->where('h_habitaciones_precios.h_habitaciones_id', $habitacion)
            ->get();

        return $this->showAll($data);
    }

    public function buscar_habitaciones(Request $request)
    {
        try {
            $inicio = null;
            $fin = null;
            $horas = null;

            if ($this->traInformacion($request->inicio)) {
                $inicio = $request->inicio;
            }
            if ($this->traInformacion($request->fin)) {
                $fin = $request->fin;
            }
            if ($this->traInformacion($request->horas)) {
                $horas = intval($request->horas);
                $minutos = intval($horas) * 60;
                $inicio = $request->inicio;
                $fin = date('Y-m-d H:i:s', strtotime("+{$minutos} minute", strtotime($inicio)));
            }

            $data = DB::table('h_habitaciones')
                ->join('h_estados', 'h_habitaciones.h_estados_id', 'h_estados.id')
                ->select(
                    'h_habitaciones.id AS id',
                    'h_habitaciones.numero AS numero',
                    'h_habitaciones.huespedes AS huespedes',
                    'h_habitaciones.descripcion AS descripcion',
                    'h_estados.nombre AS estado'
                )
                ->when($inicio && $fin && !$horas, function ($query) use ($inicio, $fin) {
                    $query->whereNotExists(function ($subquery) use ($inicio, $fin) {
                        $subquery->select(DB::raw(1))
                            ->from('h_reservaciones_detalles')
                            ->where('h_reservaciones_detalles.disponible', false)
                            ->where('h_reservaciones_detalles.horas', 0)
                            ->whereBetween(DB::RAW("'$inicio'"), [DB::RAW('DATE_FORMAT(h_reservaciones_detalles.inicio, "%Y-%m-%d")'), DB::RAW('DATE_FORMAT(h_reservaciones_detalles.fin, "%Y-%m-%d")')])
                            ->whereBetween(DB::RAW("'$fin'"), [DB::RAW('DATE_FORMAT(h_reservaciones_detalles.inicio, "%Y-%m-%d")'), DB::RAW('DATE_FORMAT(h_reservaciones_detalles.fin, "%Y-%m-%d")')])
                            ->whereRaw('h_reservaciones_detalles.h_habitaciones_id = h_habitaciones.id');
                    });
                })
                ->when($inicio && $fin && $horas, function ($query) use ($inicio, $fin) {
                    $query->whereNotExists(function ($subquery) use ($inicio, $fin) {
                        $subquery->select(DB::raw(1))
                            ->from('h_reservaciones_detalles')
                            ->where('h_reservaciones_detalles.disponible', false)
                            ->where('h_reservaciones_detalles.horas', '<>', 0)
                            ->whereBetween(DB::RAW("'$inicio'"), [DB::RAW('DATE_FORMAT(h_reservaciones_detalles.inicio, "%Y-%m-%d H:i:s")'), DB::RAW('DATE_FORMAT(h_reservaciones_detalles.fin, "%Y-%m-%d H:i:s")')])
                            ->whereBetween(DB::RAW("'$fin'"), [DB::RAW('DATE_FORMAT(h_reservaciones_detalles.inicio, "%Y-%m-%d H:i:s")'), DB::RAW('DATE_FORMAT(h_reservaciones_detalles.fin, "%Y-%m-%d H:i:s")')])
                            ->whereRaw('h_reservaciones_detalles.h_habitaciones_id = h_habitaciones.id');
                    });
                })
                ->where('h_habitaciones.h_estados_id', HEstado::DISPONIBLE)
                ->get();

            if (count($data) == 0) {
                return $this->errorResponse("Lo sentimos, no se encontro habitación disponible en las fechas seleccionadas {$inicio} - {$fin}", 423);
            }

            $habitaciones = array();
            foreach ($data as $value) {
                $buscar_fotos = HHabitacionFoto::where('h_habitaciones_id', $value->id)->get();
                $fotografias = array();

                foreach ($buscar_fotos as $value) {
                    array_push($fotografias, $value->getPictureAttribute());
                }

                $info['id'] = $value->id;
                $info['numero'] = $value->numero;
                $info['huespedes'] = $value->huespedes;
                $info['descripcion'] = $value->descripcion;
                $info['estado'] = $value->estado;
                $info['fotografias'] = $fotografias;

                array_push($habitaciones, $info);
            }

            return $this->successResponse([$habitaciones]);
        } catch (\Throwable $th) {
            return $this->errorResponse("Ocurrio un error", 500);
        }
    }
}
