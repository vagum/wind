<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CommentController;
use App\Http\Controllers\Api\Admin\PostController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\TagController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Middleware\IsModeratorMiddleware;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['prefix' => 'auth', 'middleware' => 'jwt.auth'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

//Route::group(['middleware' => ['jwt.auth', IsAdminMiddleware::class]], function () {
Route::apiResource('posts', PostController::class)->middleware('jwt.auth');
//Route::apiResource('users', UserController::class);
//Route::apiResource('tags', TagController::class);
//Route::apiResource('comments', CommentController::class);
//Route::apiResource('categories', CategoryController::class);
//Route::apiResource('roles', RoleController::class);
//Route::apiResource('profiles', ProfileController::class);
//});
