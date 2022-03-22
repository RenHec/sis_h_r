<?php

namespace App\Http\Controllers\V1\Hotel\Reservacion;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Hotel\HReservacion;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use App\Models\V1\Hotel\HReservacionDetalle;

class Reservacion extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data = array();
            switch ($request->consulta) {
                case 'r': //devuelve todas las reservaciones que se encuentran 'reservada'
                    $data = HReservacion::reservacion()->get();
                    break;
                case 'i': //devuelve todas las reservaciones que se encuentran 'reservada'
                    $data = HReservacion::in()->get();
                    break;
                case 'o':
                    $data = HReservacion::out()->get();
                    break;
                case 'p':
                    $data = HReservacion::pagado()->get();
                    break;
                case 'a':
                    $data = HReservacion::anulado()->get();
                    break;
                case 't':
                    $data = HReservacion::todas()->get();
                    break;
            }
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            /*
                $persona->nit = $request->nit;
                $persona->nombre = $request->nombre;
                $persona->telefonos = $request->telefonos;
                $persona->emails = $request->emails;
                $persona->direcciones = $request->direcciones;
                $persona->municipios_id = $request->municipios_id['id];
                $persona->usuarios_id = Auth::user()->id;            
            */
            $ciente = $this->cliente_proveedor($request);

            $reservacion = HReservacion::create(
                [
                    'codigo' => $this->generadorCodigo("R", HReservacion::whereYear('created_at', date('Y'))->count()),
                    'nombre' => $request->nombre,
                    'sub_total' => 0,
                    'extra' => 0,
                    'total' => 0,
                    'clientes_id' => $ciente->id,
                    'usuarios_id' => Auth::user()->id
                ]
            );

            $minutos = is_null($request->horas) ? 0 : intval($request->horas) * 60;
            $inicio = is_null($request->horas) ? Carbon::createFromFormat('Y-m-d', "{$request->inicio} 12:00:00") : date('Y-m-d H:i:s', strtotime($request->inicio));
            $fin = is_null($request->horas) ? Carbon::createFromFormat('Y-m-d', "{$request->fin} 12:00:00") : date('Y-m-d H:i:s', strtotime("+{$minutos} minute", strtotime($inicio)));
            $dias = is_null($request->horas) ? $inicio->diffInDays($fin) : 0;

            $cantidad_personas = 0;
            $nuevo_array = array();
            foreach ($request->h_reservaciones_detalles as $key => $value) {
                // h_reservaciones_detalles, en este valor viene el ID del precio de la habitación
                $reservado = HReservacionDetalle::where('h_habitaciones_precios_id', $value['h_reservaciones_detalles']['id'])->where('disponible', false)->first();

                if (!is_null($reservado)) {
                    throw new \Exception("La habitación {$value['h_reservaciones_detalles']['habitacion']} ya se encuentra en reservada.", 1);
                }

                $huespedes = intval($value['huespedes']);
                $precio = floatval($value['precio']);

                $mensaje_dia = $dias > 1 ? "{$dias} días" : "{$dias} día";
                $mensaje_hora = "";
                if (!is_null($request->horas)) {
                    $horas = intval($request->horas);
                    $mensaje_hora = $horas > 1 ? "{$horas} horas" : "{$horas} hora";
                }
                $formato_inicio = date('d/m/Y H:i:s', strtotime($inicio));
                $formato_fin = date('d/m/Y H:i:s', strtotime($fin));

                $detalle = HReservacionDetalle::create(
                    [
                        'codigo' => $reservacion->codigo,
                        'inicio' => $inicio,
                        'fin' => $fin,
                        'horas' => is_null($request->horas) ? 0 : $request->horas,
                        'huespedes' => $huespedes,
                        'precio' => $precio,
                        'descripcion' => is_null($request->horas) ? "La habitación #{$value['h_reservaciones_detalles']['habitacion']} fue reservado por {$mensaje_dia}, fecha de ingreso {$formato_inicio} y fecha de egreso {$formato_fin}." : "La habitación #{$value['h_reservaciones_detalles']['habitacion']} fue reservado por {$mensaje_hora}, fecha de ingreso {$formato_inicio} y fecha de egreso {$formato_fin}.",
                        'h_reservaciones_id' => $reservacion->id,
                        'h_habitaciones_precios_id' => $value['h_reservaciones_detalles']['id'],
                        'h_habitaciones_id' => $value['h_reservaciones_detalles']['habitacion_id']
                    ]
                );

                $reservacion->sub_total += $detalle->precio;
                $reservacion->total = $reservacion->sub_total;
                $reservacion->save();

                $cantidad_personas += $detalle->huespedes;
                array_push($nuevo_array, $value['h_reservaciones_detalles']['habitacion_id']);
            }
            $cantidad_habitaciones = array_unique($nuevo_array);

            DB::commit();
            return $this->successResponse("Reservacion: la reservación #{$reservacion->codigo} fue con {count($cantidad_habitaciones)} habitaciones para {$cantidad_personas} personas.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al grabar la información de la reservación');
            }
            return $e->getCode() === 1 ? $this->errorResponse($e->getMessage()) : $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HReservacion  $reservacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(HReservacion $reservacion)
    {
        try {
            $reservacion->anulado = true;
            $reservacion->save();
            return $this->successResponse("La reservación con código {$reservacion->codigo} fue dado de anulada.");
        } catch (\Exception $e) {
            return $this->errorResponse('Error en el controlador');
        }
    }
}
