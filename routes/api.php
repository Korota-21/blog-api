<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
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

// تتطلب مستخدم مسجل دخول
Route::group(['middleware'=>['auth:sanctum']],function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/user',[AuthController::class,'profile']);
});

// posts routs
Route::get('/post/all',[PostController::class,'index_all']);
Route::get('/post/{id}',[PostController::class,'show']);

// تتطلب مستخدم مسجل دخول
Route::group(['middleware'=>['auth:sanctum']],function(){
    Route::post('/post',[PostController::class,'store']);
    Route::get('/myposts/',[PostController::class,'myindex']);
    Route::put('/post/{id}',[PostController::class,'update']);
    Route::delete('/post/{id}',[PostController::class,'destroy']);

    //image routs
    Route::post('/image',[ImageController::class,'store']);
    Route::post('/image/{id}',[ImageController::class,'update']);
    Route::delete('/image/{id}',[ImageController::class,'destroy']);
});
Route::get('/image/post/{id}',[ImageController::class,'index']);
Route::get('/image/{id}',[ImageController::class,'show']);
