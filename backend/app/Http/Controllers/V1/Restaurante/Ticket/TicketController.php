<?php

namespace App\Http\Controllers\V1\Restaurante\Ticket;

use App\Traits\TicketRestaurante;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Restaurante\Caja;
use App\Models\V1\Restaurante\Venta;
use App\Traits\TicketCajaRestaurante;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;

class TicketController extends ApiController
{
    public function getVoucherCash($cashId)
    {
        try
        {
            $cashRecord = Caja::findOrFail($cashId);

            $pdf = new TicketCajaRestaurante('P', 'mm', array(80, 200));

            $pdf->AddPage();
            $pdf->setHeader();
            $pdf->setBody($cashRecord);
            $pdf->setFooter($cashRecord);

            return $pdf->Output('D');
        }
        catch (\Exception $e)
        {
            return $this->errorResponse($e, 422);
        }
    }
    public function getTicketPayment($id)
    {
        try {
            $saleRecord = Venta::findOrFail($id);

            $productsRecord = DB::table('r_orden_producto as op')
                ->join('r_producto as p', 'op.producto_id', 'p.id')
                ->select('op.id', 'p.nombre', 'op.precio', 'op.cantidad')
                ->where('op.orden_id', $saleRecord->orden_id)
                ->where('op.venta_id', $saleRecord->id)
                ->get();

            $customerRecord = DB::table('clientes')
                ->select('nombre as cliente', 'nit', 'direcciones as direccion')
                ->where('id', $saleRecord->cliente_id)
                ->first();

            $logo = Storage::disk('logo')->path("logo_empresa.png");
            $pdf = new TicketRestaurante('P', 'mm', array(80, 200));

            $pdf->AddPage();
            $pdf->Image($logo, 27, 0, 25, 0, 'PNG');
            $pdf->setHeader('Ticket', '0000000', $saleRecord->id, $saleRecord->created_at, $logo);
            $pdf->setCustomer($customerRecord);
            $pdf->setBody($productsRecord);
            $pdf->setTotal();
            $pdf->setFooter();

            return $pdf->Output('S');
        } catch (\Exception $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
