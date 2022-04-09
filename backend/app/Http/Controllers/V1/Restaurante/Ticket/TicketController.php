<?php

namespace App\Http\Controllers\V1\Restaurante\Ticket;

use App\Traits\TicketRestaurante;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Restaurante\Venta;
use App\Http\Controllers\ApiController;
class TicketController extends ApiController
{
    public function getTicketPayment($id)
    {
        $saleRecord = Venta::findOrFail($id);

        $productsRecord = DB::table('r_orden_producto as op')
                            ->join('r_producto as p','op.producto_id','p.id')
                            ->select('op.id','p.nombre','op.precio','op.cantidad')
                            ->where('op.orden_id',$saleRecord->orden_id)
                            ->where('op.venta_id',$saleRecord->id)
                            ->get();

        $customerRecord = DB::table('clientes')
                            ->select('nombre as cliente','nit','direcciones as direccion')
                            ->where('id',$saleRecord->cliente_id)
                            ->first();

        $pdf = new TicketRestaurante('P','mm',array(80,200));

        $pdf->setHeader('Ticket','0000000',$saleRecord->id, $saleRecord->created_at);
        $pdf->setCustomer($customerRecord);
        $pdf->setBody($productsRecord);
        $pdf->setTotal();
        $pdf->setFooter();

        return $pdf->Output('D');
    }
}