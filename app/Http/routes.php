<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'index', 'uses' => 'BingoController@index']);
Route::post('/start', ['as' => 'start', 'uses' => 'BingoController@start']);
Route::post('/call', ['as' => 'call', 'uses' => 'BingoController@call']);
Route::post('/reset', ['as' => 'reset', 'uses' => 'BingoController@reset']);
Route::get('/voicetext/{text}', ['as' => 'voicetext', 'uses' => 'BingoController@voicetext']);

