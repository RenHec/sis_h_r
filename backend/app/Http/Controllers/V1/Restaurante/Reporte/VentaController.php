<?php

namespace App\Http\Controllers\V1\Restaurante\Reporte;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class VentaController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getListOfSales(Request $request)
    {
        $columna    = $request['sortBy'] ? $request['sortBy'] : "nombre";
        $criterio   = $request['search'];
        $orden      = $request['sortDesc'] ? 'desc' : 'asc';
        $filas      = $request['perPage'];
        $pagina     = $request['page'];

        $ventas = DB::table('r_venta as v')
                ->join('r_orden as o','v.orden_id','o.id')
                ->join('r_tipo_orden as to','o.tipo_orden_id','to.id')
                ->join('clientes as c','v.cliente_id','c.id')
                ->select('v.id','c.nombre','v.monto','to.nombre as tipo_orden',DB::raw('DATE_FORMAT(v.created_at,"%d-%m-%Y") as fecha'))
                ->where('c.'.$columna, 'LIKE', '%' . $criterio . '%')
                ->orderBy($columna, $orden)
                ->skip($pagina)
                ->take($filas)
                ->get();

        $count = DB::table('r_venta as v')
                ->join('r_orden as o','v.orden_id','o.id')
                ->join('r_tipo_orden as to','o.tipo_orden_id','to.id')
                ->join('clientes as c','v.cliente_id','c.id')
                ->where('c.'.$columna, 'LIKE', '%' . $criterio . '%')
                ->count();

        $data = array(
            'total' => $count,
            'data' => $ventas,
        );

        return response()->json($data, 200);
    }
}
