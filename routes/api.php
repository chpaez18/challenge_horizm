<?php

use App\Http\Controllers\PostsApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/challenge/start', 'App\Http\Controllers\ChallengeController@Start');

Route::get('/users', 'App\Http\Controllers\UserController@index');
Route::get('/posts/top', 'App\Http\Controllers\PostController@top');
Route::get('/posts/{id}', 'App\Http\Controllers\PostController@show');