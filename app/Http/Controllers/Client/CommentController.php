<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreCommentCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Mail\Comment\StoredCommentReplyMail;
use App\Mail\StoredUniversalMail;
use App\Models\Comment;
use Illuminate\Support\Facades\Mail;

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
            Mail::to($comment->user->email)->send(
                new StoredUniversalMail(
                    $comment, // Передаём модель комментария
                    'Ваш комментарий получил новый лайк', // Тема письма
                    [
                        'action' => 'like',
                        'liker'  => auth()->user()->profile,
                    ]
                )
            );
        }

        // Отправляем уведомление инициатору лайка
        // Если инициатор и владелец не совпадают
        if (auth()->user()->email !== $comment->user->email) {
            Mail::to(auth()->user()->email)->send(
                new StoredUniversalMail(
                    $comment, // Модель комментария
                    'Вы поставили лайк', // Тема письма для инициатора
                    [
                        'action' => 'like_confirmation',
                        'target' => $comment,
                    ]
                )
            );
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

        // Отправляем уведомление владельцу комментария о новом ответе
        Mail::to($comment->user->email)->send(
            new StoredUniversalMail(
                $comment, // Передаем исходный комментарий, к которому добавлен ответ
                          // (можно также передать сам ответ, если так удобнее)
                'Новый ответ на ваш комментарий', // Тема письма
                [
                    'action'   => 'reply',
                    'reply'    => $reply,
                    'replier'  => auth()->user()->profile,
                ]
            )
        );

        // Отправляем уведомление инициатору ответа (подтверждение)
        // Если инициатор и владелец комментария не совпадают
        if (auth()->user()->email !== $comment->user->email) {
            Mail::to(auth()->user()->email)->send(
                new StoredUniversalMail(
                    $comment,
                    'Ваш ответ успешно отправлен',
                    [
                        'action' => 'reply_confirmation',
                        'reply'  => $reply,
                    ]
                )
            );
        }

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
