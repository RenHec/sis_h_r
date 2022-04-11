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
                DECLARE cantidadAnterior int default 0;
                DECLARE cantidadTotal int default 0;

                IF (NEW.pagado = 1) THEN

                    SET cantidadAnterior = (SELECT cantidad FROM r_producto WHERE id = NEW.producto_id);
                    SET cantidadTotal = (NEW.cantidad * cantidadAnterior);
                    UPDATE r_inventario SET stock = (stock - cantidadTotal), consumido = (consumido + cantidadTotal) WHERE producto_id = NEW.autoreferencia;

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
