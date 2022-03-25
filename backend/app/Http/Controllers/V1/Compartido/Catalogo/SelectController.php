<?php

namespace App\Http\Controllers\V1\Compartido\Catalogo;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\V1\Catalogo\Mes;
use App\Models\V1\Hotel\HEstado;
use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Principal\Cliente;
use App\Models\V1\Catalogo\Municipio;
use App\Models\V1\Principal\Proveedor;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Catalogo\Departamento;
use App\Models\V1\Catalogo\Presentacion;
use App\Models\V1\Hotel\HHabitacionFoto;
use App\Models\V1\Catalogo\SAT as V1CatalogoSAT;

class SelectController extends ApiController
{
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
            ->orderBy('h_productos.nombre')
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
                'h_kardex.stock_actual AS stock_actual',
                DB::RAW("0 AS cantidad"),
                DB::RAW("false AS incluir"),
                'h_productos.consumible'
            )
            ->where('h_productos.activo', true)
            ->where('h_kardex.activo', true)
            ->where('h_kardex.check_in', true)
            ->orderBy('h_productos.nombre')
            ->get();

        return $this->successResponse($data);
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
                DB::RAW("CONCAT('Habitación ',h_habitaciones.numero,' | Cama: ',h_tipos_camas.nombre) AS nombre_completo"),
                DB::RAW("IF(h_tipos_camas.id=1, true, false) AS mostrar")
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
                $inicio = date('Y-m-d H:i:s', strtotime("{$request->inicio} 12:00:00"));
            }
            if ($this->traInformacion($request->fin)) {
                $fin = date('Y-m-d H:i:s', strtotime("{$request->fin} 12:00:00"));
            }
            if ($this->traInformacion($request->horas)) {
                $horas = intval($request->horas);
                $minutos = intval($horas) * 60;
                $inicio = date('Y-m-d');
                $inicio = date('Y-m-d H:i:s', strtotime("{$inicio} {$request->inicio}"));
                $fin = date('Y-m-d H:i:s', strtotime("+{$minutos} minute", strtotime($inicio)));

                $fecha_actual = date("Y-m-d H:i:s");

                if ($fecha_actual > $inicio) {
                    return $this->errorResponse("Lo sentimos, no puede reservar fechas y horas pasadas. Fecha actual: {$fecha_actual} / Fecha reservación: {$inicio}", 423);
                }
            }

            $data = DB::table('h_habitaciones')
                ->join('h_estados', 'h_habitaciones.h_estados_id', 'h_estados.id')
                ->select(
                    'h_habitaciones.id AS id',
                    'h_habitaciones.numero AS numero',
                    'h_habitaciones.huespedes AS huespedes',
                    'h_habitaciones.descripcion AS descripcion',
                    'h_habitaciones.foto AS foto',
                    'h_estados.nombre AS estado'
                )
                ->whereNotExists(function ($subquery) use ($inicio) {
                    $subquery->select(DB::raw(1))
                        ->from('h_reservaciones_detalles')
                        ->where('h_reservaciones_detalles.disponible', false)
                        ->whereBetween(DB::RAW("'$inicio'"), [DB::RAW('inicio'), DB::RAW('fin')])
                        ->whereRaw('h_reservaciones_detalles.h_habitaciones_id = h_habitaciones.id');
                })
                ->whereNotExists(function ($subquery) use ($fin) {
                    $subquery->select(DB::raw(1))
                        ->from('h_reservaciones_detalles')
                        ->where('h_reservaciones_detalles.disponible', false)
                        ->whereBetween(DB::RAW("'$fin'"), [DB::RAW('inicio'), DB::RAW('fin')])
                        ->whereRaw('h_reservaciones_detalles.h_habitaciones_id = h_habitaciones.id');
                })
                ->where('h_habitaciones.h_estados_id', HEstado::DISPONIBLE)
                ->get();

            if (count($data) == 0) {
                return $this->errorResponse("Lo sentimos, no se encontro habitación disponible en las fechas seleccionadas {$inicio} - {$fin}", 423);
            }

            $habitaciones = array();
            foreach ($data as $item) {
                $buscar_fotos = HHabitacionFoto::where('h_habitaciones_id', $item->id)->get();
                $fotografias = array();

                foreach ($buscar_fotos as $value) {
                    array_push($fotografias, $value->getPictureAttribute());
                }

                $info['id'] = $item->id;
                $info['numero'] = $item->numero;
                $info['huespedes'] = $item->huespedes;
                $info['descripcion'] = $item->descripcion;
                $info['foto'] = Storage::disk('habitacion')->exists($item->foto) ? Storage::disk('habitacion')->url($item->foto) : Storage::disk('habitacion')->url('default.jpg');
                $info['estado'] = $item->estado;
                $info['fotografias'] = $fotografias;
                $info['precios'] = DB::table('h_habitaciones_precios')
                    ->join('h_habitaciones', 'h_habitaciones.id', 'h_habitaciones_precios.h_habitaciones_id')
                    ->join('h_tipos_camas', 'h_tipos_camas.id', 'h_habitaciones_precios.h_tipos_camas_id')
                    ->select(
                        'h_habitaciones_precios.id AS id',
                        'h_habitaciones_precios.precio AS precio',
                        'h_tipos_camas.nombre AS nombre',
                        'h_tipos_camas.cantidad AS cantidad',
                        'h_habitaciones.numero AS habitacion',
                        'h_habitaciones.id AS habitacion_id',
                        DB::RAW("CONCAT('Habitación ',h_habitaciones.numero,' | Cama: ',h_tipos_camas.nombre) AS nombre_completo"),
                        DB::RAW("IF(h_tipos_camas.id=1, true, false) AS mostrar"),
                        DB::RAW("false AS seleccionado")
                    )
                    ->where('h_habitaciones_precios.activo', true)
                    ->where('h_habitaciones_precios.h_habitaciones_id', $item->id)
                    ->get();
                $info['show'] = false;
                $info['ver_precio'] = false;

                array_push($habitaciones, $info);
            }

            return $this->successResponse([$habitaciones]);
        } catch (\Throwable $th) {
            return $this->errorResponse("Ocurrio un error", 500);
        }
    }

    public function buscar_nit($token)
    {
        try {
            $http = new Client();

            $ultimo_ingresado = V1CatalogoSAT::latest('id')->first();

            $correlativo = 0;
            $digito = 0;
            $codigo_departamento = 0;
            $codigo_municipio = 0;

            $contador_insercion = 0;
            $insertados = array();

            Log::warning("Inicia el proceso SAT");
            if (!is_null($ultimo_ingresado)) {
                $correlativo = $ultimo_ingresado->correlativo;
                $digito = $ultimo_ingresado->digito;
                $codigo_departamento = $ultimo_ingresado->codigo_departamento;
                $codigo_municipio = $ultimo_ingresado->codigo_municipio;
            }

            for ($i_codigo_departamento = $codigo_departamento; $i_codigo_departamento < 23; $i_codigo_departamento++) {
                Log::warning("Departamento {$i_codigo_departamento}");

                $municipios = Municipio::where('departamento_id', $i_codigo_departamento)->get();

                foreach ($municipios as $i_codigo_municipio) {
                    for ($i_digito = $digito; $i_digito < 10; $i_digito++) {
                        for ($i_correlativo = $correlativo; $i_correlativo < 99999999; $i_correlativo++) {
                            $c_correlativo = str_pad(strval($i_correlativo), 8, "0", STR_PAD_LEFT);
                            $c_digito = $i_digito;
                            $c_departamento = str_pad(strval($i_codigo_departamento), 2, "0", STR_PAD_LEFT);
                            $c_municipio = str_pad(strval($i_codigo_municipio->codigo), 2, "0", STR_PAD_LEFT);

                            $cui = "{$c_correlativo}{$c_digito}{$c_departamento}{$c_municipio}";

                            if (is_null(V1CatalogoSAT::where('cui', $cui)->first())) {
                                Log::warning("Buscando CUI {$cui}");
                                $newresponse = $http->request(
                                    'GET',
                                    "https://svc.c.sat.gob.gt/api/sat_rtu/contribuyentesIndividuales/cuis/{$cui}",
                                    [
                                        'headers' =>
                                        [
                                            'Authorization' => "Bearer {$token}"
                                        ]
                                    ]
                                )->getBody();

                                $informacion = json_decode((string) $newresponse, true);
                                Log::warning((string) $newresponse);

                                if (count($informacion) > 0) {
                                    Log::info("CUI {$cui} registrado");

                                    $sat = V1CatalogoSAT::create(
                                        [
                                            'nit' => $informacion[0]['nit'],
                                            'cui' => $informacion[0]['cui'],
                                            'primerNombre' => $informacion[0]['primerNombre'],
                                            'segundoNombre' => $informacion[0]['segundoNombre'],
                                            'tercerNombre' => $informacion[0]['tercerNombre'],
                                            'primerApellido' => $informacion[0]['primerApellido'],
                                            'segundoApellido' => $informacion[0]['segundoApellido'],
                                            'apellidoCasada' => $informacion[0]['apellidoCasada'],
                                            'fechaModifico' => date('Y-m-d H:i:s', strtotime($informacion[0]['fechaModifico'])),
                                            'correlativo' => $c_correlativo,
                                            'digito' => $c_digito,
                                            'codigo_departamento' => $c_departamento,
                                            'codigo_municipio' => $c_municipio
                                        ]
                                    );

                                    array_push($insertados, $sat->cui);
                                    $contador_insercion += 1;
                                } else {
                                    Log::error("CUI {$cui} no encontrado");
                                }
                            } else {
                                Log::warning("CUI {$cui} ya existe");
                            }
                        }
                        $correlativo = 0;
                    }
                    $i_digito = 0;
                }
            }

            return $this->successResponse(['insertados' => $contador_insercion]);
        } catch (\GuzzleHttp\Exception\ClientException  $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $responseBodyAsString = $this->traInformacion($responseBodyAsString) ? json_decode((string) $responseBodyAsString, true) : null;
            $mensaje = is_null($responseBodyAsString) ? null : "SAT: {$responseBodyAsString['error']} | {$responseBodyAsString['error_description']}";
            return $this->errorResponse($mensaje);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
