<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\V1\Restaurante\Producto;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\V1\Restaurante\CategoriaComida;
use App\Models\V1\Restaurante\ProductoCategoriaComida;

final class ProductoRestauranteImport implements ToCollection
{
    private $icono = 'mdi-food-variant';
    private $img = '';
    private $categoria;

    public function collection(Collection $collection)
    {
        foreach ($collection as $value) {
            if (!is_null($value[0]))
            {
                if($value[2] == 0)
                {
                    $this->categoria = CategoriaComida::create([
                        'nombre' => $value[1],
                        'icono' => $this->icono,
                        'orden' => 1
                    ]);

                }else{
                    $producto = Producto::create([
                        'nombre' => $value[1],
                        'precio' => $value[6],
                        'costo' => 0.00,
                        'img' => $this->img,
                        'consumo_reservacion' => 2,
                        'quien_prepara' => $value[4],
                        'usa_inventario' => $value[3],
                        'descripcion' => $value[7],
                    ]);

                    ProductoCategoriaComida::create([
                        'categoria_comida_id' => $this->categoria->id,
                        'producto_id' => $producto->id
                    ]);
                }
            }
        }
    }
}
