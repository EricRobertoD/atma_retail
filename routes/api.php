<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Auth::routes(['verify' => true]);

Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::get('email-verification', 'App\Http\Controllers\AuthController@verify')->name('verification.verify');
Route::post('loginUser', 'App\Http\Controllers\AuthController@loginUser');
Route::post('loginAdmin', 'App\Http\Controllers\AuthController@loginAdmin');
Route::get('buku', 'App\Http\Controllers\BookController@index');
Route::post('logout', 'App\Http\Controllers\AuthController@logout');

Route::middleware(['auth:sanctum', 'ability:web'])->group(function(){

    Route::get('profile', 'App\Http\Controllers\AuthController@profile');
    Route::post('update', 'App\Http\Controllers\AuthController@update');
    
    Route::post('bukuTransaksi', 'App\Http\Controllers\BookTransactionController@store');
    Route::get('bukuTransaksi', 'App\Http\Controllers\BookTransactionController@index');
    Route::put('pengembalianBuku/{booksTransaction}', 'App\Http\Controllers\BookTransactionController@pengembalian');
    Route::delete('bukuTransaksi/{booksTransaction}', 'App\Http\Controllers\BookTransactionController@destroy');

});


Route::middleware(['auth:sanctum', 'ability:admin'])->group(function(){

    Route::put('update/{user}', 'App\Http\Controllers\AuthController@updateAdmin');

    Route::post('buku', 'App\Http\Controllers\BookController@store');
    Route::put('buku/{books}', 'App\Http\Controllers\BookController@update');
    Route::delete('buku/{books}', 'App\Http\Controllers\BookController@destroy');

});