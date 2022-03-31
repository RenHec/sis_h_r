<?php

namespace App\Http\Controllers\V1\Restaurante\Reporte;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class CajaController extends ApiController
{
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
