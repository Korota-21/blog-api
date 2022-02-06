<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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
Route::post("/register", [AuthController::class, "register"]);
Route::post('/login', [AuthController::class, 'login']);
Route::get('is_login/{id}', [UserController::class, 'is_login']);

// تتطلب مستخدم مسجل دخول
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [UserController::class, 'usersList']);
    Route::get('/users/{id}', [UserController::class, 'profile']);
});

// posts routs
Route::group(["prefix" => "post"], function () {
    //all specific user posts
    Route::get('user/{user_id}', [PostController::class, 'index']);
    //all posts
    Route::get('/all', [PostController::class, 'index_all']);
    Route::get('/{id}', [PostController::class, 'show']);
    // تتطلب مستخدم مسجل دخول
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('', [PostController::class, 'store']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
        Route::post('/{post_id}/comment', [CommentController::class, 'store']);
    });
});
//comment routs
Route::group(["prefix" => "comment"], function () {
    Route::get('/', [CommentController::class, 'index']);
    Route::get('/{id}', [CommentController::class, 'show']);
    // تتطلب مستخدم مسجل دخول
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::put('/{id}', [CommentController::class, 'update']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
    });
});

//image routs
Route::group(["prefix" => "image"], function () {
    Route::get('/post/{id}', [ImageController::class, 'index']);
    Route::get('/{id}', [ImageController::class, 'show']);
    // تتطلب مستخدم مسجل دخول
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/', [ImageController::class, 'store']);
        Route::post('/{id}', [ImageController::class, 'update']);
        Route::delete('/{id}', [ImageController::class, 'destroy']);
    });
});
