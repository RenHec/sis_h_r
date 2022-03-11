<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Prefix route: api/v1/restaurante */

Route::resource('/menus','Menu\MenuController');
Route::resource('/estado-orden','EstadoOrden\EstadoOrdenController',['except' => ['create','edit']]);

Route::resource('/tipo-orden','TipoOrden\TipoOrdenController',['except' => ['create','edit']]);
Route::get('/tipo-orden-list','TipoOrden\TipoOrdenController@listOrderType');

Route::resource('/categoria-comida','CategoriaComida\CategoriaComidaController',['except' => ['create','edit']]);
Route::get('/categoria-comida-list','CategoriaComida\CategoriaComidaController@listFoodCategory');

Route::resource('/productos','Producto\ProductoController',['except' => ['create','edit']]);
Route::get('/productos-list','Producto\ProductoController@productsList');

Route::resource('/ordenes','Orden\OrdenController',['except' => ['create','edit']]);
