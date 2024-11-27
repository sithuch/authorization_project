<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

Route::post('/login', [Controller::class, 'login']);
Route::post('/refresh', [Controller::class, 'refresh']);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return DB::select("SELECT * FROM users");
});

