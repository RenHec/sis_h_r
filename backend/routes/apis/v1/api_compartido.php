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

//rutas para AuthController
Route::name('me')->get('auth/me', 'Usuario\AuthController@me');
Route::name('login')->post('auth/login', 'Usuario\AuthController@login');
Route::name('logout')->get('auth/logout', 'Usuario\AuthController@logout');

//rutas para UsuarioController
Route::resource('user', 'Usuario\UsuarioController')->except('create', 'edit', 'show');
Route::name('user.password')->post('user_password', 'Usuario\UsuarioController@cambiar_password');

//rutas para UsuarioRolController
Route::resource('user_rol', 'Usuario\UsuarioRolController')->except('index', 'create', 'edit', 'update');

//rutas para RolController
Route::resource('rol', 'Rol\RolController')->except('create', 'edit', 'show', 'update');

//rutas para RolMenuController
Route::resource('rol_menu', 'Rol\RolMenuController')->except('index', 'create', 'edit', 'show', 'update');
Route::name('rol_menu.eliminar_masivo')->post('rol_menu/eliminar_masivo', 'Rol\RolMenuController@eliminario_masiva');

//rutas para MenuController
Route::resource('menu', 'Menu\MenuController')->only('index');

//rutas para SelectController
Route::name('select.bitacora')->get('select/bitacora', 'Catalogo\SelectController@bitacora');
Route::name('select.departamento')->get('select/departamento', 'Catalogo\SelectController@departamento');
Route::name('select.municipio')->get('select/municipio', 'Catalogo\SelectController@municipio');
Route::name('select.presentacion')->get('select/presentacion', 'Catalogo\SelectController@presentacion');
Route::name('select.cliente')->get('select/cliente', 'Catalogo\SelectController@cliente');
Route::name('select.proveedor')->get('select/proveedor', 'Catalogo\SelectController@proveedor');
Route::name('select.mes')->get('select/mes', 'Catalogo\SelectController@mes');
Route::name('select.tipo_pago')->get('select/tipo_pago', 'Catalogo\SelectController@tipo_pago');
Route::name('select.tipo_cama')->get('select/tipo_cama', 'Catalogo\SelectController@tipo_cama');
Route::name('select.estado_habitacion')->get('select/estado_habitacion', 'Catalogo\SelectController@estado_habitacion');
Route::name('select.producto_insumo')->get('select/producto_insumo', 'Catalogo\SelectController@producto_insumo');
Route::name('select.producto_check_in')->get('select/producto_check_in', 'Catalogo\SelectController@producto_check_in');
Route::name('select.precios')->get('select/precios/{habitacion}', 'Catalogo\SelectController@precios');
Route::name('select.buscar_habitaciones')->get('select/buscar_habitaciones', 'Catalogo\SelectController@buscar_habitaciones');
Route::name('select.buscar_nit')->get('select/buscar_nit/{token}', 'Catalogo\SelectController@buscar_nit');
Route::name('select.empleado')->get('select/empleado', 'Catalogo\SelectController@empleado');

//rutas para PresentacionController
Route::resource('presentacion', 'Catalogo\PresentacionController')->only(['index', 'store', 'update', 'destroy']);

//rutas para ClienteController
Route::resource('cliente_management', 'Persona\ClienteController')->only(['index', 'update', 'destroy']);

//rutas para ProveedorController
Route::resource('proveedor_management', 'Persona\ProveedorController')->only(['index', 'update', 'destroy']);

//rutas para EmpleadoController
Route::resource('empleado', 'Persona\EmpleadoController')->except(['create', 'edit']);
