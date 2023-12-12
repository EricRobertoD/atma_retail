<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::get('email-verification', 'App\Http\Controllers\AuthController@verify')->name('verification.verify');
Route::post('loginUser', 'App\Http\Controllers\AuthController@loginUser');

Route::get('buku', 'App\Http\Controllers\BookController@index');
Route::post('buku', 'App\Http\Controllers\BookController@store');
Route::put('buku/{books}', 'App\Http\Controllers\BookController@update');
Route::delete('buku/{books}', 'App\Http\Controllers\BookController@destroy');

Route::get('bukuTransaksi', 'App\Http\Controllers\BookTransactionController@index');
Route::post('bukuTransaksi', 'App\Http\Controllers\BookTransactionController@store');
Route::put('pengembalianBuku/{booksTransaction}', 'App\Http\Controllers\BookTransactionController@pengembalian');
Route::delete('buku/{booksTransaction}', 'App\Http\Controllers\BookTransactionController@destroy');
