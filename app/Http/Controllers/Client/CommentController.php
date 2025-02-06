<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreCommentCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Jobs\Comment\StoreCommentReplySendMailJob;
use App\Jobs\Comment\ToggleLikeCommentSendMailJob;
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

        // Если лайк был добавлен, отправляем уведомление владельцу комментария
        if ($isLiked) {
            ToggleLikeCommentSendMailJob::dispatch($comment, auth()->user()->profile, auth()->user()->email)->onQueue('comment-mail');
        }

        return response()->json([
            'is_liked' => $isLiked,
            'likes' => $likesCount,
        ]);
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

        StoreCommentReplySendMailJob::dispatch($comment, $reply, auth()->user()->profile, auth()->user()->email)->onQueue('comment-mail');

        return CommentResource::make($reply)->resolve();
    }

    public function indexReplies(Comment $comment): array
    {
        // Получаем ответы для данного комментария, можно добавить withCount, если нужен уровень вложенности
        $replies = $comment->replies()
            ->withCount('replies')
            ->with('profile') // если нужно для отображения автора
            ->get();

        return CommentResource::collection($replies)->resolve();
    }
}
