<?php

namespace App\Http\Controllers\V1\Hotel\Reservacion;

use Illuminate\Http\Request;
use App\Models\V1\Hotel\HTipoCama;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Hotel\HReservacion;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HHabitacionPrecio;
use App\Models\V1\Hotel\HReservacionDetalle;

class ReservacionController extends ApiController
{
    private $controlador_principal = 'ReservacionController';

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
                case 'i': //devuelve todas las reservaciones que se encuentran en check in
                    $data = HReservacion::in()->get();
                    break;
                case 'o': //devuelve todas las reservaciones que se encuentran en check out
                    $data = HReservacion::out()->get();
                    break;
                case 'a': //devuelve todas las reservaciones que se encuentran anuladas
                    $data = HReservacion::anulado()->get();
                    break;
                case 't': //devuelve todas las reservaciones registradas
                    $data = HReservacion::todas()->get();
                    break;
            }
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            return $this->errorResponse('Error del controlador');
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
                    'usuarios_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $inicio = null;
            $fin = null;
            $horas = null;

            if ($this->traInformacion($request->inicio)) {
                $inicio = date('Y-m-d H:i:s', strtotime("{$request->inicio} 12:00:00"));
                $formato_inicio = date('d/m/Y H:i:s', strtotime("{$request->inicio} 12:00:00"));
            }
            if ($this->traInformacion($request->fin)) {
                $fin = date('Y-m-d H:i:s', strtotime("{$request->fin} 12:00:00"));
                $formato_fin = date('d/m/Y H:i:s', strtotime("{$request->fin} 12:00:00"));
            }
            if ($request->consulta_por_hora) {
                $horas = intval($request->horas);
                $minutos = intval($horas) * 60;
                $inicio = date('Y-m-d');
                $inicio = date('Y-m-d H:i:s', strtotime("{$inicio} {$request->inicio}"));
                $fin = date('Y-m-d H:i:s', strtotime("+{$minutos} minute", strtotime($inicio)));

                $formato_inicio = date('d/m/Y H:i:s', strtotime($inicio));
                $formato_fin = date('d/m/Y H:i:s', strtotime("+{$minutos} minute", strtotime($inicio)));
            }

            $dias = $request->dias;

            $cantidad_personas = 0;
            $nuevo_array = array();
            foreach ($request->h_reservaciones_detalles as $value) {
                // h_reservaciones_detalles, en este valor viene el ID del precio de la habitación
                if (is_null($horas) && $value['h_reservaciones_detalles']['seleccionado'] == 1) {
                    $reservado = HReservacionDetalle::join('h_reservaciones', 'h_reservaciones_detalles.h_reservaciones_id', 'h_reservaciones.id')
                        ->where('h_habitaciones_precios_id', $value['h_reservaciones_detalles']['id'])
                        ->whereBetween(DB::RAW('inicio'), [DB::RAW("'$inicio'"), DB::RAW("'$fin'")])
                        ->whereBetween(DB::RAW('fin'), [DB::RAW("'$inicio'"), DB::RAW("'$fin'")])
                        ->where('h_reservaciones.anulado', false)
                        ->first();
                } else if (!is_null($horas) && $value['h_reservaciones_detalles']['seleccionado'] == 1) {
                    $reservado = HReservacionDetalle::join('h_reservaciones', 'h_reservaciones_detalles.h_reservaciones_id', 'h_reservaciones.id')
                        ->where('h_habitaciones_precios_id', $value['h_reservaciones_detalles']['id'])
                        ->whereBetween(DB::RAW("{$request->inicio} 12:00:01"), [DB::RAW('inicio'), DB::RAW('fin')])
                        ->whereBetween(DB::RAW("{$request->fin} 11:59:59"), [DB::RAW('inicio'), DB::RAW('fin')])
                        ->where('h_reservaciones.anulado', false)
                        ->first();
                }

                if (!is_null($reservado)) {
                    throw new \Exception("La habitación {$value['h_reservaciones_detalles']['habitacion']} ya se encuentra reservada.", 1);
                }

                $huespedes = intval($value['huespedes']);
                $precio = floatval($value['precio']);

                $mensaje_hora = "";
                if ($request->consulta_por_hora) {
                    $mensaje_hora = $horas > 1 ? "{$horas} horas" : "{$horas} hora";
                } else {
                    $mensaje_dia = $dias > 1 ? "{$dias} días" : "{$dias} día";
                }

                $tol = $huespedes * $dias;
                $incluye_desayuno = boolval($value['h_reservaciones_detalles']['incluye_desayuno']) ? " e incluye {$tol} desayunos." : ".";

                $detalle = HReservacionDetalle::create(
                    [
                        'codigo' => $reservacion->codigo,
                        'inicio' => $inicio,
                        'fin' => $fin,
                        'dias' => $request->consulta_por_hora ? 0 : $dias,
                        'horas' => $request->consulta_por_hora ? $horas : 0,
                        'huespedes' => $huespedes,
                        'precio' => $precio,
                        'sub_total' => $request->consulta_por_hora ? $precio : $dias * $precio,
                        'descripcion' => !$request->consulta_por_hora ? "La habitación #{$value['h_reservaciones_detalles']['habitacion']} fue reservado por {$mensaje_dia}, fecha de ingreso {$formato_inicio} y fecha de egreso {$formato_fin}{$incluye_desayuno}" : "La habitación #{$value['h_reservaciones_detalles']['habitacion']} fue reservado por {$mensaje_hora}, fecha de ingreso {$formato_inicio} y fecha de egreso {$formato_fin}.",
                        'h_reservaciones_id' => $reservacion->id,
                        'h_habitaciones_precios_id' => $value['h_reservaciones_detalles']['id'],
                        'h_habitaciones_id' => $value['h_reservaciones_detalles']['habitacion_id'],
                        'incluye_desayuno' => $value['h_reservaciones_detalles']['incluye_desayuno'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );

                $this->bitacora_general($detalle->getTable(), $this->acciones(0), $detalle, "{$this->controlador_principal}@store");

                $extra = 0;

                if ($detalle->incluye_desayuno) {
                    $buscar_desayuno = HHabitacionPrecio::find($value['h_reservaciones_detalles']['id']);
                    $extra = $buscar_desayuno->incluye_desayuno ? ($huespedes * $detalle->dias) * $buscar_desayuno->precio_desayuno : 0;
                }

                $reservacion->extra += $extra;
                $reservacion->sub_total += $detalle->sub_total;
                $reservacion->total = $reservacion->sub_total;
                $reservacion->updated_at = date('Y-m-d H:i:s');
                $reservacion->save();

                $cantidad_personas += $detalle->huespedes;
                array_push($nuevo_array, $value['h_reservaciones_detalles']['habitacion_id']);
            }
            $cantidad_habitaciones = count(array_unique($nuevo_array));

            $ticket = $this->ticketReservacion($reservacion);
            $path = "/hotel/comprobante/{$reservacion->codigo}.pdf";
            Storage::disk('ticket')->put($path, $ticket);

            $reservacion->comprobante = $path;
            $reservacion->save();

            $this->bitacora_general($reservacion->getTable(), $this->acciones(0), $reservacion, "{$this->controlador_principal}@store");

            DB::commit();
            return $this->successResponse(['mensaje' => "Reservacion: la reservación #{$reservacion->codigo} fue con {$cantidad_habitaciones} habitaciones para {$cantidad_personas} personas.", 'comprobante' => $reservacion->getTicketAttribute()]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la información de la reservación.');
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
            DB::beginTransaction();

            HReservacionDetalle::where('h_reservaciones_id', $reservacion->id)->update(['disponible' => true, 'updated_at' => date('Y-m-d H:i:s')]);
            $detalle = HReservacionDetalle::where('h_reservaciones_id', $reservacion->id)->get();
            foreach ($detalle as $item) {
                $this->bitacora_general($item->getTable(), $this->acciones(3), $item, "{$this->controlador_principal}@destroy");
            }
            $reservacion->anulado = true;
            $reservacion->save();
            $this->bitacora_general($reservacion->getTable(), $this->acciones(3), $reservacion, "{$this->controlador_principal}@destroy");

            DB::commit();
            return $this->successResponse("La reservación con código {$reservacion->codigo} fue anulada.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            return $this->errorResponse('Error en el controlador');
        }
    }
}
