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
Route::resource('/categoria-comida','CategoriaComida\CategoriaComidaController',['except' => ['create','edit']]);
