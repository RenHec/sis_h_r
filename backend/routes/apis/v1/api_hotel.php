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

/* Prefix route: servicio/hotel/version_uno */

Route::resource('/tipo_cama', 'Habitacion\TipoCama')->only('index', 'store', 'show', 'destroy');
Route::resource('/habitacion', 'Habitacion\Habitacion')->only('index', 'store', 'update', 'destroy');
Route::resource('/habitacion_foto', 'Habitacion\HabitacionFoto')->only('show', 'update', 'destroy');
Route::resource('/habitacion_precio', 'Habitacion\HabitacionPrecio')->only('update', 'destroy');

Route::resource('/insumo', 'Insumo\Insumo')->only('index', 'store', 'destroy');
Route::resource('/kardex', 'Insumo\Kardex')->only('index', 'store', 'show', 'update', 'destroy');
