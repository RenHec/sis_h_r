<?php

namespace App\Http\Controllers\V1\Restaurante\Reporte;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Restaurante\Caja;
use App\Models\V1\Restaurante\CajaEgreso;

class CajaController extends ApiController
{
    public function closeCash(Request $request)
    {
        $rules = [
            'fecha'=>'required|date',
            'hora'=>'required'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){

            $caja = Caja::where('activo',1)->first();

            if(!$caja){
                return $this->errorResponse('No existe una caja activa');
            }

            $tipoPago = TipoPago::where('ticket',0)->first();

            $ventas = DB::table('r_venta')
                        ->select(DB::raw('SUM(monto) as total'))
                        ->where('tipo_pago_id',$tipoPago->id)
                        ->where('caja_id',$caja->id)
                        ->first();

            $caja->ingresos = $ventas->total;
            $caja->fecha_cierre = $request->get('fecha');
            $caja->hora_cierre = $request->get('hora');
            $caja->activo = 0;
            $caja->save();

            return $this->showMessage($caja,200);
        });
    }
    public function storePurcharses(Request $request)
    {
        $rules = [
            'comprobante'=>'required',
            'descripcion'=>'required',
            'monto' => 'required|numeric|min:0'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use($request){

            $caja = Caja::where('activo',1)->first();

            if(!$caja){
                return $this->errorResponse('No existe una caja activa');
            }

            $registro = new CajaEgreso();
            $registro->comprobante = $request->get('comprobante');
            $registro->descripcion = $request->get('descripcion');
            $registro->monto = $request->get('monto');
            $registro->caja_id = $caja->id;
            $registro->usuario_id = $request->user()->id;
            $registro->save();

            $caja->egresos = $caja->egresos + $request->get('monto');
            $caja->save();

            return $this->showMessage('',201);
        });
    }
    public function openCash(Request $request)
    {
        $rules = [
            'fecha'=>'required|date',
            'hora'=>'required',
            'saldo' => 'required|numeric|min:0'
        ];

        $this->validate($request, $rules);

        if($this->verifyExistsCashOpened()){
            return $this->errorResponse('Ya existe una apertura de caja activa');
        }

        $registro = new Caja();
        $registro->fecha_apertura = $request->get('fecha');
        $registro->hora_apertura = $request->get('hora');
        $registro->saldo_inicial = $request->get('saldo');
        $registro->usuario_id = $request->user()->id;
        $registro->save();

        return $this->showMessage($registro,200);
    }

    public function verifyExistsCashOpened()
    {
        $caja = Caja::where('activo',1)->get();

        return (count($caja) > 0) ? true : false;
    }

    public function verifyIfExistsCashOpened()
    {
        $caja = Caja::where('activo',1)->first();

        return response()->json(['data'=> $caja],200);
    }

    public function getSalesAmountReportByDate(Request $request)
    {
        $rules = [
            'desde'=>'required|date',
            'hasta'=>'required|date'
        ];

        $this->validate($request, $rules);

        $desde = Carbon::parse($request->get('desde'));
        $hasta = Carbon::parse($request->get('hasta'));

        if($desde > $hasta)
        {
            throw new \Exception("Debe seleccionar un rango válido", 1);
        }

        $reporte = DB::table('r_venta as v')
                    ->join('tipos_pagos as tp','v.tipo_pago_id','tp.id')
                    ->select(DB::raw('SUM(v.monto) as monto'),'tp.nombre as forma')
                    ->groupBy('tp.nombre')
                    ->whereBetween(DB::raw('DATE(v.created_at)'),[$desde,$hasta])
                    ->get();

        return response()->json(['data' => $reporte],200);
    }
}
