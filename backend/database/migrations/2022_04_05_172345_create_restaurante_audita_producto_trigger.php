<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestauranteAuditaProductoTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER Ingreso_Producto AFTER INSERT ON `r_producto` FOR EACH ROW
            BEGIN
                IF (NEW.usa_inventario = 1) THEN
                    INSERT INTO r_inventario (`producto_id`, `created_at`, `updated_at`)
                    VALUES (NEW.id, now(), now());
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
        DB::unprepared('DROP TRIGGER IF EXISTS `Ingreso_Producto`');
    }
}
