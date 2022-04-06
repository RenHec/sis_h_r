<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestauranteAuditaVentaProductoTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER Venta_Producto AFTER UPDATE ON `r_orden_producto` FOR EACH ROW
            BEGIN
                IF (NEW.activo = 0) THEN
                    UPDATE r_inventario SET stock = (stock - NEW.cantidad), consumido = (consumido + NEW.cantidad) WHERE producto_id = NEW.producto_id;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `Venta_Producto`');
    }
}
