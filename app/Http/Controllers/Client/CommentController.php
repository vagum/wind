<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreCommentCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Переключение лайка для комментария.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleLike(Comment $comment)
    {
        // Переключаем лайк для текущего пользователя
        $res = $comment->likedProfiles()->toggle(auth()->user()->profile->id);

        // Определяем, добавлен или удален лайк
        $isLiked = count($res['attached']) > 0;

        // Получаем обновленное количество лайков
        $likesCount = $comment->likedProfiles()->count();

        return response()->json([
            'is_liked' => $isLiked,
            'likes' => $likesCount,
        ]);

//        if ($comment->likes()->where('user_id', $user->id)->exists()) {
//            // Если лайк уже поставлен, удаляем его
//            $comment->likes()->where('user_id', $user->id)->delete();
//            $isLiked = false;
//        } else {
//            // Иначе, ставим лайк
//            $comment->likes()->create(['user_id' => $user->id]);
//            $isLiked = true;
//        }
//
//        // Возвращаем обновлённое количество лайков и статус
//        return response()->json([
//            'is_liked' => $isLiked,
//            'likes' => $comment->likes()->count(),
//        ]);
    }

    /**
     * Добавление ответа к комментарию.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return array
     */
    public function storeReply(StoreCommentCommentRequest $request, Comment $comment)
    {
        $data = $request->validated();

        $reply = $comment->replies()->create([
            'profile_id' => auth()->user()->profile->id,
            'content' => $data['content'],
            'post_id' => $comment->post_id,
        ]);

//        // Загрузка связанных данных (например, профиля пользователя)
//        $reply->load(['likedProfiles']); // Убедитесь, что загружаете лайки

        return CommentResource::make($reply)->resolve();
    }
}
