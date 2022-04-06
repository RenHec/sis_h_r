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
            CREATE DEFINER = CURRENT_USER TRIGGER `Venta_Producto` AFTER UPDATE ON `r_orden_producto` FOR EACH ROW
            BEGIN
                DECLARE stock_anterio int default 0;
                SET stock_anterio = (SELECT stock FROM r_inventario WHERE producto_id = NEW.producto_id ORDER BY id DESC LIMIT 1);
                
                IF (NEW.activo = 0) THEN
                    INSERT INTO r_inventario (producto_id, stock, consumido) VALUES (NEW.producto_id, (stock_anterio - NEW.cantidad), NEW.cantidad);
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
