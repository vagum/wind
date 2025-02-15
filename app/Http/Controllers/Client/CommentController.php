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
        $profileId = auth()->check() && auth()->user()->profile
            ? auth()->user()->profile->id
            : null;

        // Загружаем реплаи с:
        // 1) Количеством лайков (liked_profiles_count),
        // 2) Флагом is_liked (есть ли лайк от текущего пользователя),
        // 3) Количеством ответов (replies_count).
        // 4) Профилем автора (и user, если нужно в ресурсе).
        $replies = $comment->replies()
            ->with([
                'profile.user', // если нужно обращаться к user внутри ProfileResource или CommentResource
            ])
            ->withCount([
                'likedProfiles',  // получим liked_profiles_count
                'replies',        // получим replies_count (дочерние реплаи)
            ])
            ->withExists([
                // Флаг "is_liked"
                'likedProfiles as is_liked' => function ($query) use ($profileId) {
                    if ($profileId) {
                        $query->where('profiles.id', $profileId);
                    }
                }
            ])
            ->get();

        return CommentResource::collection($replies)->resolve();
    }

}
