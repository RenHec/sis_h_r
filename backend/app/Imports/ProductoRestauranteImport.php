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
                        'precio' => $value[8],
                        'costo' => 0.00,
                        'promocion' => $value[3],
                        'img' => $this->img,
                        'consumo_reservacion' => 0,
                        'quien_prepara' => $value[6],
                        'usa_inventario' => $value[5],
                        'descripcion' => $value[9],
                    ]);

                    $producto->autoreferencia = $producto->id;
                    $producto->save();

                    ProductoCategoriaComida::create([
                        'categoria_comida_id' => $this->categoria->id,
                        'producto_id' => $producto->id
                    ]);
                }
            }
        }
    }
}
