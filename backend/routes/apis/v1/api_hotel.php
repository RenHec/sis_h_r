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

Route::resource('/tipo_cama', 'Habitacion\TipoCamaController')->only('index', 'store', 'show', 'destroy');
Route::resource('/habitacion', 'Habitacion\HabitacionController')->only('index', 'store', 'update', 'destroy');
Route::resource('/habitacion_foto', 'Habitacion\HabitacionFotoController')->only('show', 'update', 'destroy');
Route::resource('/habitacion_precio', 'Habitacion\HabitacionPrecioController')->only('update', 'destroy');

Route::resource('/insumo', 'Insumo\InsumoController')->only('index', 'store', 'destroy');
Route::resource('/kardex', 'Insumo\KardexController')->only('index', 'store', 'show', 'update', 'destroy');

Route::resource('/reservacion', 'Reservacion\ReservacionController')->only('index', 'store', 'destroy');
Route::resource('/check_in', 'Reservacion\CheckInController')->only('show', 'store', 'destroy');
Route::resource('/check_out', 'Reservacion\CheckOutController')->only('show', 'store', 'destroy');

Route::resource('/pago', 'Pago\PagoController')->only('index', 'show', 'store', 'destroy');
