<?php

namespace App\Http\Controllers\V1\Restaurante\Producto;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\V1\Restaurante\Producto;
use App\Models\V1\Restaurante\ProductoCategoriaComida;

class ProductoController extends ApiController
{

    protected $pathImage   = '/img-restaurant/';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columna    = $request['sortBy'] ? $request['sortBy'] : "nombre";
        $criterio   = $request['search'];
        $orden      = $request['sortDesc'] ? 'desc' : 'asc';
        $filas      = $request['perPage'];
        $pagina     = $request['page'];

        $productos  = DB::table('r_producto')
                    ->select('id','nombre','precio','img')
                    ->where($columna, 'LIKE', '%' . $criterio . '%')
                    ->orderBy($columna, $orden)
                    ->skip($pagina)
                    ->take($filas)
                    ->get();

        $count      = DB::table('r_producto')
                    ->where($columna, 'LIKE', '%' . $criterio . '%')
                    ->count();

        $data       = array(
                        'total' => $count,
                        'data' => $productos,
                    );

        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->get('id')){
            return $this->update($request, $request->get('id'));
        }

        $rules = [
            'nombre' => 'required|string',
            'precio' => 'required|numeric|min:1',
            'imagen' => 'required|image',
            'categorias' => 'nullable|array'
        ];

        $this->validate($request, $rules);

        $file    = $request->file('imagen');
        $name   = $file->hashName();

        $file->move(getcwd().$this->pathImage,$name);

        return DB::transaction(function() use($request, $name){
            $registro           = new Producto();
            $registro->nombre   = $request->get('nombre');
            $registro->precio   = $request->get('precio');
            $registro->img      = $this->pathImage.$name;
            $registro->save();

            foreach($request->get('categorias') as $key => $value)
            {
                ProductoCategoriaComida::create([
                    'producto_id'           => $registro->id,
                    'categoria_comida_id'   => $value
                ]);
            }

            return $this->showMessage('',201);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $registro = Producto::with('producto_categoria_comida')
                        ->where('producto.id',$id)
                        ->first();

        return $this->showOne($registro);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'nombre' => 'required|string',
            'precio' => 'required|numeric|min:1',
            'imagen' => 'nullable|image',
            'categorias' => 'nullable|array'
        ];

        $this->validate($request, $rules);

        $registro  = Producto::findOrFail($id);

        return DB::transaction(function() use($request, $registro){

            $registro->producto_categoria_comida()->delete();

            foreach($request->get('categorias') as $key => $value)
            {
                ProductoCategoriaComida::create([
                    'producto_id'           => $registro->id,
                    'categoria_comida_id'   => $value
                ]);
            }

            $registro->nombre   = $request->get('nombre');
            $registro->precio   = $request->get('precio');

            if($request->hasFile('imagen')){
                $file    = $request->file('imagen');
                $name    = $file->hashName();

                $file->move(getcwd().$this->pathImage,$name);

                $currentImage = getcwd().$registro->img;

                if(file_exists($currentImage)){
                    unlink(realpath($currentImage));
                }

                $registro->img = $this->pathImage.$name;
            }

            $registro->save();

            return $this->showMessage('',202);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $registro = Producto::findOrFail($id);

        return DB::transaction(function() use($registro){
            $currentImage = getcwd().$registro->img;

            if(file_exists($currentImage)){
                unlink(realpath($currentImage));
            }

            $registro->producto_categoria_comida()->delete();

            $registro->delete();

            return $this->showMessage('',210);
        });
    }

    public function productsList()
    {
        $registros =  Producto::select('id','nombre','precio','img')
                        ->with('producto_categoria_comida')
                        ->where('r_producto.activo',1)
                        ->get();

        return response()->json(['data' => $registros]);
    }
}
