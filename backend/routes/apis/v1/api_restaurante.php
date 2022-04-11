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
Route::get('/departamentos','Miscelaneo\DepartamentoController@index');
Route::get('municipios/{id}','Miscelaneo\MunicipioController@show');
Route::resource('clientes','Miscelaneo\ClienteController',['only'=>['store','show']]);

Route::resource('/mesas','Mesa\MesaController', ['except' => ['create','edit']]);
Route::get('/mesas-list','Mesa\MesaController@listTables');

Route::resource('/menus','Menu\MenuController');

Route::resource('/tipos-pagos','TipoPago\TipoPagoController',['only' => ['index']]);

Route::resource('/estado-orden','EstadoOrden\EstadoOrdenController',['except' => ['create','edit']]);
Route::get('/estado-orden-list','EstadoOrden\EstadoOrdenController@getAllOrderStatus');

Route::resource('/tipo-orden','TipoOrden\TipoOrdenController',['except' => ['create','edit']]);
Route::get('/tipo-orden-list','TipoOrden\TipoOrdenController@listOrderType');

Route::resource('/categoria-comida','CategoriaComida\CategoriaComidaController',['except' => ['create','edit']]);
Route::get('/categoria-comida-list','CategoriaComida\CategoriaComidaController@listFoodCategory');

Route::resource('/productos','Producto\ProductoController',['except' => ['create','edit']]);
Route::get('/productos-list','Producto\ProductoController@productsList');
Route::post('/productos-delete-inventory','Producto\ProductoController@deleteInventoryOfProduct');

Route::post('/productos-promotion','Producto\DetalleProductoController@storePromotionProducts');

Route::resource('/inventario','Inventario\InventarioController',['except' => ['create','edit']]);

Route::resource('/ordenes','Orden\OrdenController',['except' => ['create','edit']]);
Route::get('/ordenes-list','Orden\OrdenController@orderList');
Route::post('/ordenes-payment','Orden\OrdenController@orderPayment');
Route::post('/ordenes-status','Orden\OrdenController@updateOrderStatus');

Route::get('/ordenes-edit-list/{orderId}','Orden\OrdenDetailController@getDetailOrder');
Route::post('/ordenes-minus','Orden\OrdenDetailController@setMinusQuantity');
Route::post('/ordenes-plus','Orden\OrdenDetailController@setPlusQuantity');
Route::post('/ordenes-delete-one','Orden\OrdenDetailController@deleteOneDetailOrder');
Route::post('/ordenes-delete-all','Orden\OrdenDetailController@deleteAllOrderDetail');
Route::post('/ordenes-modify-state','Orden\OrdenDetailController@modifyStateAllOrderDetail');

Route::post('/reporte-restaurante-caja','Reporte\CajaController@getSalesAmountReportByDate');
Route::get('/reporte-restaurante-venta','Reporte\VentaController@getListOfSales');

Route::get('/ticket-restaurante-pago/{id}','Ticket\TicketController@getTicketPayment');
