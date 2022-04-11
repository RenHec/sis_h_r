<?php

namespace App\Http\Controllers\V1\Restaurante\Producto;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\V1\Restaurante\Producto;
use App\Models\V1\Restaurante\ProductoCategoriaComida;

class DetalleProductoController extends ApiController
{
    protected $pathImage   = '/img-restaurant/';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function storePromotionProducts(Request $request)
    {
        $message = [
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric'  => 'El precio debe de ser un número.',
            'precio.min'  => 'El precio debe ser mayor a :min .'
        ];

        $rules = [
            'nombre' => 'required|string',
            'precio' => 'required|numeric|min:1',
            'imagen' => 'required|image',
            'categorias' => 'nullable|array',
            'costo' => 'required|numeric',
            'cantidad' => 'required|numeric',
            'preparacion' => 'required|numeric|min:1|max:2',
            'consumo_reservacion' => 'required',
            'descripcion' => 'nullable',
            'productoId' => 'required|numeric'
        ];

        $this->validate($request, $rules, $message);

        $file    = $request->file('imagen');
        $name   = $file->hashName();

        $file->move(getcwd() . $this->pathImage, $name);

        return DB::transaction(function () use ($request, $name) {
            $registro                       = new Producto();
            $registro->nombre               = $request->get('nombre');
            $registro->autoreferencia       = $request->get('productoId');
            $registro->precio               = $request->get('precio');
            $registro->img                  = $this->pathImage . $name;
            $registro->costo                = $request->get('costo');
            $registro->cantidad                = $request->get('cantidad');
            $registro->descripcion          = $request->get('descripcion');
            $registro->quien_prepara        = $request->get('preparacion');
            $registro->promocion            = 1;
            $registro->usa_inventario       = 0;
            $registro->consumo_reservacion  = $request->get('consumo_reservacion');
            $registro->save();

            foreach ($request->get('categorias') as $key => $value) {
                ProductoCategoriaComida::create([
                    'producto_id'           => $registro->id,
                    'categoria_comida_id'   => $value
                ]);
            }

            return $this->showMessage('', 201);
        });
    }

}
