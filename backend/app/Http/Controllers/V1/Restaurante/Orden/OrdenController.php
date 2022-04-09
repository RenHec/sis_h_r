<?php

namespace App\Http\Controllers\V1\Restaurante\Orden;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\V1\Restaurante\Orden;
use App\Models\V1\Restaurante\Venta;
use App\Http\Controllers\ApiController;
use App\Models\V1\Hotel\HReservacion;
use App\Models\V1\Hotel\HReservacionDetalle;
use App\Models\V1\Restaurante\Producto;
use App\Models\V1\Restaurante\EstadoOrden;
use App\Models\V1\Restaurante\OrdenProducto;

class OrdenController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $finaliza = EstadoOrden::select('id')
            ->where('finaliza', 1)
            ->first();

        $registros = DB::table('r_orden as o')
            ->join('r_tipo_orden as to', 'o.tipo_orden_id', 'to.id')
            ->join('r_estado_orden as eo', 'o.estado_orden_id', 'eo.id')
            ->join('r_mesa as m', 'o.mesa_id', 'm.id')
            ->select('o.id', 'o.monto', 'o.fecha', 'o.hora', 'to.nombre as tipo_orden', 'eo.nombre as estado_orden', 'eo.id as estado_orden_id', 'eo.icono', 'm.nombre as mesa', 'eo.finaliza', 'eo.color')
            ->where('eo.id', '<>', $finaliza->id)
            ->orderBy('m.orden', 'asc')
            ->get();

        $datos = array();

        foreach ($registros as $key => $value) {

            $detalle = DB::table('r_orden_producto as op')
                ->join('r_producto as p', 'op.producto_id', 'p.id')
                ->select('op.id', 'op.cantidad', 'op.notas', 'p.nombre as producto', 'p.img', 'op.precio')
                ->where('op.orden_id', $value->id)
                ->where('op.activo', 1)
                ->where('p.quien_prepara', Producto::COCINA)
                ->get();

            $datos[$key]            = (array)$value;
            $datos[$key]['detalle'] = $detalle;
        }

        return response()->json(['data' => $datos], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'id'            => 'required',
            'monto'         => 'required|numeric|min:0',
            'tipo_orden_id' => 'required|numeric',
            'fecha'         => 'required|date',
            'hora'          => 'required',
            'detalle'       => 'required|array',
            'mesa_id'       => 'required|numeric|min:1',
            'reservacion'   => 'nullable|numeric|min:0|max:1',
            'no_reservacion' => 'nullable'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function () use ($request) {

            $huespedes = 0;
            $desayunos_servidos = 0;
            $existe_reservacion = null;
            if ($request->get('monto') == 0) {
                if ($request->get('reservacion') == 1 && empty($request->get('no_reservacion'))) {
                    return $this->errorResponse('Hace falta el número de reservación');
                }

                //Verificar si codigo de reservación existe
                $existe_reservacion = HReservacion::where('codigo', mb_strtoupper($request->get('no_reservacion')))->where('check_in', true)->first();
                if (is_null($existe_reservacion)) {
                    return $this->errorResponse('El número de reservación es incorrecto');
                }

                $reservaciones = HReservacionDetalle::where('h_reservaciones_id', $existe_reservacion->id)->where('incluye_desayuno', true)->get();
                $dias = 0;
                foreach ($reservaciones as $item) {
                    $dias = $item->dias;
                    $huespedes += $item->huespedes;
                }
                $cantidad_desayunos = $dias * $huespedes;

                //Verificar si tiene items gratis todavia
                $ordenes_reservacion = OrdenProducto::join('r_producto', 'r_producto.id', '.r_orden_producto.producto_id')
                    ->where('reservacion_id', mb_strtoupper($request->get('no_reservacion')))
                    ->where('consumo_reservacion', true)
                    ->get();

                foreach ($ordenes_reservacion as $item) {
                    $desayunos_servidos += $item->cantidad;
                }
            }

            $estado = EstadoOrden::select('id')
                ->where('inicia', 1)
                ->first();

            $registro = $this->ensureExistsOrderActive($request->get('mesa_id'));


            if ($registro) {
                $registro->monto            += $request->get('monto');
                $registro->estado_orden_id  = $estado->id;
                $registro->save();
            } else {
                $registro                   = new Orden();
                $registro->id               = $request->get('id');
                $registro->monto            = $request->get('monto');
                $registro->tipo_orden_id    = $request->get('tipo_orden_id');
                $registro->fecha            = $request->get('fecha');
                $registro->hora             = $request->get('hora');
                $registro->mesa_id          = $request->get('mesa_id');
                $registro->usuario_id       = $request->user()->id;
                $registro->estado_orden_id  = $estado->id;
                $registro->save();
            }

            foreach ($request->get('detalle') as $value) {

                if ($value['reservacion'] == 0 && $request->get('reservacion') == 1) {
                    return $this->errorResponse('Hay un producto no válido para este tipo de orden');
                }

                if ($desayunos_servidos == 0) {
                    $desayunos_servidos += $value['cantidad'];
                }

                if (!($desayunos_servidos < ($huespedes + 1)) && !is_null($existe_reservacion)) {
                    return $this->errorResponse("La reservación con código {$existe_reservacion->codigo} execede la cantidad de platillos disponibles {$desayunos_servidos}/{$huespedes}.");
                }

                OrdenProducto::create([
                    'cantidad'      => $value['cantidad'],
                    'orden_id'      => $registro->id,
                    'producto_id'   => $value['id'],
                    'precio'        => $request->get('reservacion') == 1 ? 0 : $value['precio'],
                    'reservacion_id' => mb_strtoupper($request->get('no_reservacion'))
                ]);
            }

            return $this->showMessage('', 201);
        });
    }

    public function ensureExistsOrderActive($id)
    {
        $orden = Orden::select('id', 'monto')
            ->where('mesa_id', $id)
            ->where('activo', 1)
            ->first();

        if (!$orden) {
            return null;
        }

        return $orden;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $registros = DB::table('r_orden as o')
            ->join('r_tipo_orden as to', 'o.tipo_orden_id', 'to.id')
            ->join('r_estado_orden as eo', 'o.estado_orden_id', 'eo.id')
            ->join('r_mesa as m', 'o.mesa_id', 'm.id')
            ->select('o.id', 'o.monto', 'o.fecha', 'o.hora', 'to.nombre as tipo_orden', 'eo.nombre as estado_orden', 'eo.id as estado_orden_id', 'eo.icono', 'm.nombre as mesa', 'eo.finaliza', 'eo.color')
            ->where('o.id', $id)
            ->where('o.activo', 1)
            ->orderBy('m.orden', 'asc')
            ->get();
        $datos = array();

        foreach ($registros as $key => $value) {

            $detalle = DB::table('r_orden_producto as op')
                ->join('r_producto as p', 'op.producto_id', 'p.id')
                ->select('op.id', 'op.cantidad', 'op.notas', 'p.nombre as producto', 'p.img', 'op.precio')
                ->where('op.orden_id', $value->id)
                ->where('op.pagado', 0)
                ->get();

            $datos            = (array)$value;
            $datos['detalle'] = $detalle;
        }

        return response()->json(['data' => $datos], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function orderList()
    {
        $ordenes    = DB::table('r_orden as o')
            ->join('r_estado_orden as eo', 'eo.id', 'o.estado_orden_id')
            ->join('r_tipo_orden as to', 'to.id', 'o.tipo_orden_id')
            ->join('r_mesa as m', 'o.mesa_id', 'm.id')
            ->select('o.id', 'o.monto', 'o.fecha', 'o.hora', 'eo.nombre as estado_orden', 'to.nombre as tipo_orden', 'eo.color', 'eo.icono', 'eo.finaliza', 'eo.inicia', 'm.nombre as mesa')
            ->where('o.activo', 1)
            ->orderBy('m.orden', 'asc')
            ->get();

        return response()->json(['data' => $ordenes], 200);
    }

    public function updateOrderStatus(Request $request)
    {
        $rules = [
            'orden'            => 'required',
            'estado'           => 'required'
        ];

        $this->validate($request, $rules);

        if ($this->verifyIfFinishedStatus($request->get('estado'))) {
            $this->updateProductStatus($request->get('orden'));
        }

        $registro = Orden::findOrFail($request->get('orden'));
        $registro->estado_orden_id = $request->get('estado');
        $registro->save();

        return $this->showMessage('', 204);
    }

    public function updateProductStatus($id)
    {
        $detalle =  DB::table('r_orden_producto')
            ->where('orden_id', $id)
            ->update(['activo' => 0]);
        return;
    }

    public function verifyIfFinishedStatus($id)
    {
        $registro = EstadoOrden::findOrFail($id);

        return $registro->finaliza == 1;
    }

    public function orderPayment(Request $request)
    {
        $rules = [
            'id'            => 'required',
            'orden_id'      => 'required',
            'tipo_pago_id'  => 'required',
            'cliente_id'    => 'required',
            'monto'         => 'required',
            'voucher'       => 'nullable',
            'detalle'       => 'required|array',
            'ticket'        => 'required|numeric|min:0|max:1'
        ];

        $this->validate($request, $rules);

        if (!$this->ensureHasNotOrderInKitchen($request->get('orden_id'))) {
            return $this->errorResponse('La orden tiene productos pendientes de despacharse');
        }

        if($request->get('ticket') == 1 && empty($request->get('voucher'))){
            return $this->errorResponse('Debe ingresar un número de voucher válido');
        }

        return DB::transaction(function () use ($request) {
            $registro               = new Venta();
            $registro->id           = $request->get('id');
            $registro->orden_id     = $request->get('orden_id');
            $registro->tipo_pago_id = $request->get('tipo_pago_id');
            $registro->cliente_id   = $request->get('cliente_id');
            $registro->voucher      = $request->get('voucher');
            $registro->usuario_id   = $request->user()->id;
            $registro->monto        = $request->get('monto');
            $registro->save();

            $orden = Orden::findOrFail($request->get('orden_id'));

            if ($orden->monto > $request->get('monto')) //será pago dividido
            {
                $orden->monto = ($orden->monto - $request->get('monto'));
                $orden->cuenta_dividida = 1;
            }

            if ($orden->monto == $request->get('monto')) //se completará el pago
            {
                if ($orden->cuenta_dividida == 1) {
                    $orden->monto = $this->ensureJoinSeparatePayments($orden->id);
                }

                $orden->activo = 0;
            }
            $orden->save();

            $this->updateItemsOrderProducts($request->get('detalle'), $registro->id);

            return $this->showMessage('', 201);
        });
    }

    public function ensureJoinSeparatePayments($ordenId)
    {
        $registro = DB::table('r_orden_producto')
            ->select(DB::raw('SUM(precio*cantidad) as monto'))
            ->where('orden_id', $ordenId)
            ->first();

        return $registro->monto;
    }

    public function updateItemsOrderProducts($detalle,$venta)
    {
        $detalle =  DB::table('r_orden_producto')
            ->whereIn('id', $detalle)
            ->update(['pagado' => 1, 'venta_id' => $venta]);
        return;
    }

    public function ensureHasNotOrderInKitchen($ordenId)
    {
        $finaliza = EstadoOrden::select('id')
            ->where('finaliza', 1)
            ->first();

        $orden = Orden::findOrFail($ordenId);

        return $orden->estado_orden_id == $finaliza->id;
    }
}
