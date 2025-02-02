<?php

use App\Http\Controllers\Client\CommentController;
use App\Http\Controllers\Client\PostController;
use Illuminate\Support\Facades\Route;

//Route::group(['middleware' => 'auth'], function () {

Route::get('posts', [PostController::class, 'index'])->name('clients.posts.index');
Route::get('posts/{post}', [PostController::class, 'show'])->name('clients.posts.show');
Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('clients.post.destroy');
Route::post('posts/{post}/likes', [PostController::class, 'toggleLike'])->name('posts.likes.toggle');
Route::post('posts/{post}/comments', [PostController::class,'storeComment'])->name('posts.comments.store');
Route::get('posts/{post}/comments', [PostController::class,'indexComment'])->name('posts.comments.index');

Route::get('comments/{comment}/replies', [CommentController::class, 'indexReplies'])->name('comments.replies.index');
Route::post('comments/{comment}/replies', [CommentController::class, 'storeReply'])->name('comments.replies.store');
Route::post('comments/{comment}/likes', [CommentController::class, 'toggleLike'])->name('comments.likes.toggle');

//});
