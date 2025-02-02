<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\IndexRequest;
use App\Http\Requests\Post\StoreCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Post\PostResource;
use App\Mail\Comment\StoredCommentMail;
use App\Mail\StoredUniversalMail;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class PostController extends Controller
{
    public function index(IndexRequest $request)
    {
        $data = $request->validationData();
//        dd($data);
        $posts = Post::filter($data)->orderBy('id','desc')->paginate($data['per_page'],'*','page',$data['page']);
        $posts = PostResource::collection($posts);

        // Массив с фильтрами и их типами
        $active_filters = [
            'title' => 'text',
            'content' => 'text',
            'published_at_from' => 'date',
            'views_from' => 'number',
        ];

        if(Request::wantsJson()){
            return $posts;
        }

        // Передаем массив $active_filters вместе с $posts
        return inertia('Client/Post/Index', [
            'posts'          => $posts,
            'active_filters' => $active_filters,
        ]);
//        $posts = PostResource::collection(Post::all())->resolve();
//        return inertia("Client/Post/Index", compact("posts"));
    }

    public function show(Post $post)
    {
        $post = PostResource::make($post)->resolve();
        return inertia("Client/Post/Show", compact("post"));
    }

    public function store()
    {
        $postData = [
            'author' => 'Ivan 5',
            'title' => 'My Post 5',
            'category' => 'PHP',
            'image_path' => 'some path',
            'tag' => 'Cat',
            'description' => 'description',
            'published_at' => '2020-12-20',
            'likes' => 30,
            'views' => 150,
            'status' => 2,
            'is_published' => true,
        ];

        return Post::create($postData);
    }

    public function update(Post $post)
    {
        $postData = [
            'author' => 'Ivan 5 Edited',
            'title' => 'My Post 5 Edited',
            'category' => 'PHP',
            'image_path' => 'some path',
            'tag' => 'Cat',
            'description' => 'description',
            'published_at' => '2020-12-20',
            'likes' => 30,
            'views' => 150,
            'status' => 2,
            'is_published' => true,
        ];

        $post->update($postData);

        return $post;
    }

    public function destroy(Post $post)
    {

        $post->delete();

        return response(['message' => 'Post has been deleted']);
    }

    public function storeComment(Post $post, StoreCommentRequest $request): array
    {
        $data = $request->validationData();

        $comment = auth()->user()->profile->comments()->create($data);

        // Отправляем уведомление владельцу поста о новом комментарии
        Mail::to($post->user->email)->send(
            new StoredUniversalMail(
                $post, // Модель поста
                'Новый комментарий к вашему посту', // Тема письма
                [
                    'action'    => 'comment',
                    'comment'   => $comment,
                    'commenter' => auth()->user()->profile,
                ]
            )
        );

        // Отправляем уведомление автору комментария (подтверждение)
        // Если автор комментария и владелец поста не совпадают
        if (auth()->user()->email !== $post->user->email) {
            Mail::to(auth()->user()->email)->send(
                new StoredUniversalMail(
                    $post,
                    'Ваш комментарий успешно отправлен',
                    [
                        'action'  => 'comment_confirmation',
                        'comment' => $comment,
                    ]
                )
            );
        }

        return CommentResource::make($comment)->resolve();
    }

    public function indexComment(Post $post): array
    {

        // Получаем только корневые комментарии с количеством ответов
        $rootComments = $post->comments()
            ->whereNull('parent_id')
            ->withCount('replies')
            ->with('profile') // если нужно для отображения автора
            ->get();

        return CommentResource::collection($rootComments)->resolve();
    }

    public function toggleLike(Post $post): JsonResponse
    {
        // Переключаем лайк для текущего пользователя
        $res = $post->likedProfiles()->toggle(auth()->user()->profile->id);

        // Определяем, добавлен или удален лайк
        $isLiked = count($res['attached']) > 0;

        // Получаем обновленное количество лайков
        $likesCount = $post->likedProfiles()->count();

        // Если лайк был добавлен, отправляем уведомление владельцу поста
        if ($isLiked) {
            // Здесь передаём модель поста, тему и дополнительные данные, например, кто поставил лайк
            Mail::to($post->user->email)->send(
                new StoredUniversalMail(
                    $post, // Модель поста
                    'Ваш пост получил новый лайк',
                    [
                        'action' => 'like',
                        'liker' => auth()->user()->profile,
                    ]
                )
            );
        }

        // Отправляем уведомление инициатору лайка (подтверждение), если он не является владельцем поста
        if (auth()->user()->email !== $post->user->email) {
            Mail::to(auth()->user()->email)->send(
                new StoredUniversalMail(
                    $post, // Передаём модель поста
                    'Вы поставили лайк', // Тема письма для инициатора лайка
                    [
                        'action' => 'like_confirmation',
                        'target' => $post,
                    ]
                )
            );
        }

        return response()->json([
            'is_liked' => $isLiked,
            'likes' => $likesCount,
        ]);

    }
}

