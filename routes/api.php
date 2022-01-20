<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

//auth routs
Route::post("/register",[AuthController::class,"register"]);
Route::post('/login',[AuthController::class,'login']);
Route::group(['middleware'=>['auth:sanctum']],function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/user',[AuthController::class,'profile']);
});

// posts routs

Route::get('/post',[PostController::class,'index']);
Route::group(['middleware'=>['auth:sanctum']],function(){
Route::apiResource("post",PostController::class);
});
