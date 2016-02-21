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

Route::get('/', function () {
    return '';
});
Route::get('/stats', 'EntryController@stats');
Route::get('/start', 'EntryController@start');
Route::get('/cancel', 'EntryController@cancel');
Route::get('/stop', 'EntryController@stop');
