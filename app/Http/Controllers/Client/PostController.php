<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\IndexRequest;
use App\Http\Requests\Post\StoreCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Post\PostResource;
use App\Jobs\Post\StoreCommentPostSendMailJob;
use App\Jobs\Post\ToggleLikePostSendMailJob;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class PostController extends Controller
{
    public function index(IndexRequest $request)
    {
        $data = $request->validationData();
//        dd($data);

        $cacheName = 'posts.' . md5(json_encode($data));

        $posts = Cache::remember($cacheName, now()->addMinutes(100), function () use ($data) {
            return Post::filter($data)->orderBy('id', 'desc')->with([
                'category',
                'profile.user',
                'tags',
                'likedProfiles',
                'comments',
                'viewedProfiles'
            ])->withCount([
                'likedProfiles',
                'parentComments',
                'viewedProfiles'
            ])->paginate($data['per_page'], '*', 'page', $data['page']);
        });

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
        // Определяем, есть ли залогиненный пользователь и, соответственно, его профиль
        $profileId = null;
        if (auth()->check() && auth()->user()->profile) {
            $profileId = auth()->user()->profile->id;
        }

        // Вставляем новую запись в таблицу post_profile_views для каждого запроса
        DB::table('post_profile_views')->insert([
            'post_id'    => $post->id,
            'profile_id' => $profileId, // если гость, то будет NULL
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Загружаем необходимые отношения для поста, чтобы избежать дублирования запросов
        $post->load([
            'category',
            'profile.user',     // загрузка профиля с пользователем для ProfileResource
            'tags',
            'likedProfiles',
            'parentComments',
        ])->loadCount([
            'viewedProfiles',
            'parentComments',
        ]);

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

        // обнуляем кеш постов в PostgreSQL, для редис и мемкешед есть tags и удаляется по другому
        DB::table('cache')->where('key', 'like', 'posts.%')->delete();

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
        Gate::authorize('delete', $post);

        $post->delete();

        // обнуляем кеш постов в PostgreSQL, для редис и мемкешед есть tags и удаляется по другому
        DB::table('cache')->where('key', 'like', 'posts.%')->delete();

        return response(['message' => 'Post has been deleted']);
    }

    public function storeComment(Post $post, StoreCommentRequest $request): array
    {
        $data = $request->validated();

        // Создаём комментарий, привязываем к текущему профилю
        // и назначаем нужный post_id
        $comment = auth()->user()->profile->comments()->create([
            'post_id'   => $post->id,
            'content'   => $data['content'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        // Запускаем джоб на отправку письма (если нужно)
        StoreCommentPostSendMailJob::dispatch(
            $post,
            $comment,
            auth()->user()->profile,
            auth()->user()->email
        )->onQueue('post-mail');

        // Подгружаем профиль, пользователя, счётчик лайков (liked_profiles_count) и флаг is_liked
        // чтобы не было дополнительных запросов на фронте
        $profileId = auth()->check() ? auth()->user()->profile->id : null;

        $comment->load([
            'profile.user',  // профиль + пользователь
        ])
            ->loadCount([
                'replies',
                'likedProfiles', // нужно, чтобы получить liked_profiles_count
            ])
            ->loadExists([
                'likedProfiles as is_liked' => function ($query) use ($profileId) {
                    if ($profileId) {
                        $query->where('profiles.id', $profileId);
                    }
                }
            ]);

        // Возвращаем ресурс, который теперь содержит все нужные поля
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
            ToggleLikePostSendMailJob::dispatch($post, auth()->user()->profile, auth()->user()->email)->onQueue('post-mail');
        }

        return response()->json([
            'is_liked' => $isLiked,
            'likes' => $likesCount,
        ]);

    }
}

