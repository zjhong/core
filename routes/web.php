<?php

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

use Illuminate\Support\Facades\Route;

/**
 * websocket route
 */
Route::middleware('auth:api')->any('/ws', function () { //wss://domain/ws
    //WebSocket连接的路径要经过Authenticate之类的中间件
});
