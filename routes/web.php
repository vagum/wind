<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Route::controller(PostController::class)->group(function () {
//    Route::get('/posts/index', 'index');
//    Route::get('/posts/{post}/show', 'show');
//    Route::get('/posts/store', 'store');
//    Route::get('/posts/{post}/update', 'update');
//    Route::get('/posts/{post}/destroy', 'destroy');
//});
//
//Route::controller(CategoryController::class)->group(function () {
//    Route::get('/categories/index', 'index');
//    Route::get('/categories/{category}/show', 'show');
//    Route::get('/categories/store', 'store');
//    Route::get('/categories/{category}/update', 'update');
//    Route::get('/categories/{category}/destroy', 'destroy');
//});
//
//Route::controller(CommentController::class)->group(function () {
//    Route::get('/comments/index', 'index');
//    Route::get('/comments/{comment}/show', 'show');
//    Route::get('/comments/store', 'store');
//    Route::get('/comments/{comment}/update', 'update');
//    Route::get('/comments/{comment}/destroy', 'destroy');
//});
//
//Route::controller(ProfileController::class)->group(function () {
//    Route::get('/profiles/index', 'index');
//    Route::get('/profiles/{profile}/show', 'show');
//    Route::get('/profiles/store', 'store');
//    Route::get('/profiles/{profile}/update', 'update');
//    Route::get('/profiles/{profile}/destroy', 'destroy');
//});
//
//Route::controller(RoleController::class)->group(function () {
//    Route::get('/roles/index', 'index');
//    Route::get('/roles/{role}/show', 'show');
//    Route::get('/roles/store', 'store');
//    Route::get('/roles/{role}/update', 'update');
//    Route::get('/roles/{role}/destroy', 'destroy');
//});
//
//Route::controller(TagController::class)->group(function () {
//    Route::get('/tags/index', 'index');
//    Route::get('/tags/{tag}/show', 'show');
//    Route::get('/tags/store', 'store');
//    Route::get('/tags/{tag}/update', 'update');
//    Route::get('/tags/{tag}/destroy', 'destroy');
//});
//
//Route::controller(UserController::class)->group(function () {
//    Route::get('/users/index', 'index');
//    Route::get('/users/{user}/show', 'show');
//    Route::get('/users/store', 'store');
//    Route::get('/users/{user}/update', 'update');
//    Route::get('/users/{user}/destroy', 'destroy');
//});
//
//Route::controller(LikeController::class)->group(function () {
//    Route::get('/likes/index', 'index');
//    Route::get('/likes/{like}/show', 'show');
//    Route::get('/likes/store', 'store');
//    Route::get('/likes/{like}/update', 'update');
//    Route::get('/likes/{like}/destroy', 'destroy');
//});
//
//Route::controller(ViewController::class)->group(function () {
//    Route::get('/views/index', 'index');
//    Route::get('/views/{view}/show', 'show');
//    Route::get('/views/store', 'store');
//    Route::get('/views/{view}/update', 'update');
//    Route::get('/views/{view}/destroy', 'destroy');
//});
