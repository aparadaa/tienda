<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::resource('profile', 'ProfileController', ['only' => ['index', 'detail', 'store']]);
});

Route::group(['middleware' => ['auth']], function () {
    Route::namespace ('Clientes')->prefix('clientes')->name('clientes.')->group(function () {
        Route::resource('clientes', 'ClientesController')->only(['show', 'store']);
    });
});

Route::group(['middleware' => ['auth', 'cancerbero']], function () {
    Route::get('/', ['as' => 'index.index', 'uses' => 'HomeController@index']);

    Route::namespace ('Catalogs')->prefix('catalogs')->name('catalogs.')->group(function () {
        Route::resource('users', 'UsersController');
        Route::resource('roles', 'RolesController');
    });

    Route::namespace ('Compras')->prefix('compras')->name('compras.')->group(function () {
        Route::resource('productos', 'ProductosController');
    });

    Route::namespace ('Bodegas')->prefix('bodegas')->name('bodegas.')->group(function () {
        Route::resource('bodegas', 'BodegasController');
        Route::resource('bodegas-usuarios', 'UserBodegasController');
        Route::resource('movimientos', 'MovimientosController');
    });
    // Route::namespace ('Bodegas')->prefix('bodegas')->name('bodegas.')->group(function () {
    //     Route::namespace ('Movimientos')->prefix('movimientos')->name('movimientos.')->group(function () {
    //         Route::resource('movimientos', 'MovimientosController');
    //     });
    //     Route::resource('recepcion', 'RecepcionController');

    // });

});
