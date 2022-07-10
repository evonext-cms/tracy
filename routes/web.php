<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

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

use Illuminate\Support\Facades\Route;

Route::get('/bar', [
    'as'   => 'bar',
    'uses' => 'LaravelTracyController@bar',
]);
