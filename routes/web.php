<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

Route::get('/', function() {
    return 'Welcome to ' . env('APP_NAME');
});

Route::get('/wallet/list', 'WalletController@listWallets');
Route::post('/create', 'WalletController@create');
Route::post('/transaction', 'WalletController@transaction');
