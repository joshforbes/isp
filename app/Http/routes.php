<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/stats/{type}', 'EntryController@stats');
Route::get('/start/{type}', 'EntryController@start');
Route::get('/cancel/{type}', 'EntryController@cancel');
Route::get('/stop/{type}', 'EntryController@stop');
