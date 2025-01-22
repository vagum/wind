<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show']
    ]);
    Route::resource('comments', CommentController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show']
    ]);
    Route::resource('posts', PostController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show', 'destroy', 'edit', 'update']
    ]);
//    Route::controller(PostController::class)->group(function () {
//        Route::post('/posts/{post}', 'update')->name('admin.posts.update');
//    });
    Route::resource('profiles', ProfileController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show']
    ]);
    Route::resource('roles', RoleController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show']
    ]);
    Route::resource('tags', TagController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show']
    ]);
    Route::resource('users', UserController::class, [
        'as' => 'admin',
        'only' => ['index', 'create', 'store', 'show']
    ]);
});
