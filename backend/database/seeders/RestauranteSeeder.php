<?php

namespace Database\Seeders;

use App\Models\V1\Restaurante\CategoriaComida;
use App\Models\V1\Restaurante\EstadoOrden;
use App\Models\V1\Restaurante\TipoOrden;
use Illuminate\Database\Seeder;

class RestauranteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EstadoOrden::insert([
            ['nombre'=>'Pendiente','icono'=>'fas fa-clock-o','inicia'=>1,'finaliza'=>0,'orden'=>1,'color'=>'red darken-1'],
            ['nombre'=>'En preparación','icono'=>'fas fa-spinner','inicia'=>0,'finaliza'=>0,'orden'=>2,'color'=>'blue darken-1'],
            ['nombre'=>'Preparado','icono'=>'fas fa-check','inicia'=>0,'finaliza'=>0,'orden'=>3,'color'=>'yellow darken-2'],
            ['nombre'=>'Servido','icono'=>'fas fa-thumbs-up','inicia'=>0,'finaliza'=>1,'orden'=>4,'color'=>'green']
        ]);

        TipoOrden::insert([
            ['nombre'=>'Consumo local','orden' => 1],
            ['nombre'=>'Para llevar','orden' => 2]
        ]);

        CategoriaComida::insert([
            ['nombre'=>'Desayuno','orden'=>1,'icono'=>'mdi-food-variant'],
            ['nombre'=>'Almuerzo','orden'=>2,'icono'=>'mdi mdi-pasta'],
            ['nombre'=>'Cena','orden'=>3,'icono'=>'mdi-noodles'],
            ['nombre'=>'Refacciones','orden'=>4,'icono'=>'mdi-food'],
            ['nombre'=>'Bebidas','orden'=>5,'icono'=>'mdi-bottle-soda'],
        ]);


    }
}
