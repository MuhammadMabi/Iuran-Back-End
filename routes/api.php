<?php

use Illuminate\Http\Request;
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

Route::group(['middleware' => 'api','prefix' => 'auth'], function ($router) {
    Route::post('/login', 'AuthController@login');
    Route::post('/register', 'AuthController@register');
    Route::put('/changepassword', 'AuthController@changePassword');
    Route::post('/logout', 'AuthController@logout');
    Route::post('/refresh', 'AuthController@refresh');
    Route::get('/me', 'AuthController@me');
    Route::get('/', 'AuthController@index');
    Route::get('/show/{nik}', 'AuthController@show');
    Route::put('/update/{nik}', 'AuthController@update');
    Route::put('/updatepassword/{nik}', 'AuthController@updatePassword');
    Route::delete('/delete/{nik}', 'AuthController@destroy');
});

Route::get('/petugas', 'AuthController@showPetugas');

// Route Keterangan Rumah
Route::prefix('rumah')->group(function () {
    Route::get('/','KeteranganRumahController@index');
    Route::get('/{no_kk}','KeteranganRumahController@show');
    Route::post('/create','KeteranganRumahController@store');
    Route::put('/update/{no_kk}','KeteranganRumahController@update');
    Route::delete('/delete/{no_kk}','KeteranganRumahController@destroy');
});
Route::get('/getkk','KeteranganRumahController@getkk');

// Route Jenis Iuran
Route::prefix('iuran')->group(function () {
    Route::get('/', 'JenisIuranController@index');  
    Route::get('/{id}', 'JenisIuranController@show');
    Route::post('/create', 'JenisIuranController@store');
    Route::put('/update/{id}', 'JenisIuranController@update');
    Route::delete('/delete/{id}', 'JenisIuranController@destroy');
});

// Route Uang Masuk
Route::prefix('masuk')->group(function () {
    Route::get('/','UangMasukController@index');
    Route::get('/{id}','UangMasukController@show');
    Route::post('/create','UangMasukController@store');
    Route::put('/update/{id}','UangMasukController@update');
    Route::delete('/delete/{id}','UangMasukController@destroy');
});
Route::get('payments','UangMasukController@payments');
Route::get('/listTagihan','UangMasukController@ListTagihan');
Route::get('/totalUangMasuk','UangMasukController@TotalUangMasuk');
Route::get('/dataqr/{no_kk}','UangMasukController@dataQr');
Route::get('/generate/{no_kk}','UangMasukController@getQr');

// Route Uang Keluar
Route::prefix('keluar')->group(function () {
    Route::get('/','UangKeluarController@index');
    Route::get('/{id}','UangKeluarController@show');
    Route::post('/create','UangKeluarController@store');
    Route::put('/update/{id}','UangKeluarController@update');
    Route::delete('/delete/{id}','UangKeluarController@destroy');
});
