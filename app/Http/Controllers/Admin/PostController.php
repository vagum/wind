<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Post\IndexRequest;
use App\Http\Requests\Admin\Post\StoreRequest;
use App\Http\Requests\Admin\Post\UpdateRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PostController extends Controller
{
    public function index(IndexRequest $request): AnonymousResourceCollection|Response
    {
        $data = $request->validationData();
//        dd($data);

        $cacheName = 'posts.' . md5(json_encode($data));

        $posts = Cache::remember($cacheName, now()->addMinutes(100), function () use ($data) {
            return Post::filter($data)->orderBy('id','desc')->with([
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
        ])->paginate($data['per_page'],'*','page',$data['page']);
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
        return inertia('Admin/Post/Index', [
            'posts'          => $posts,
            'active_filters' => $active_filters,
        ]);
    }

    public function show(Post $post): Response
    {
        $profileId = auth()->check() && auth()->user()->profile
            ? auth()->user()->profile->id
            : null;

        // Запись о просмотре
        DB::table('post_profile_views')->insert([
            'post_id'    => $post->id,
            'profile_id' => $profileId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $post->load([
            'category',
            'profile.user', // для ProfileResource
            'tags',
            'parentComments',
        ])->loadCount([
            'parentComments',
            'viewedProfiles',
            'likedProfiles', // добавляем счётчик лайков
        ]);

        // Если нужно знать «лайкнуто» ли текущим пользователем, добавьте:
        $post->loadExists([
            'likedProfiles as is_liked' => function ($query) use ($profileId) {
                if ($profileId) {
                    $query->where('profiles.id', $profileId);
                }
            }
        ]);

        $post = PostResource::make($post)->resolve();
        return inertia('Admin/Post/Show', compact('post'));
    }

    public function edit(Post $post): Response
    {
        // Загружаем необходимые отношения для поста, чтобы избежать дополнительных запросов в ресурсах.
        $post->load([
            'category',
            'profile.user', // для ProfileResource, если используется доступ к пользователю профиля
            'tags',
            'likedProfiles',
        ])->loadCount([
            'viewedProfiles',
        ]);

        $post = PostResource::make($post)->resolve();
        $categories = CategoryResource::collection(Category::all())->resolve();

        // обнуляем кеш постов в PostgreSQL, для редис и мемкешед есть tags и удаляется по другому
        DB::table('cache')->where('key', 'like', 'posts.%')->delete();

        return inertia('Admin/Post/Edit', compact('post', 'categories'));
    }

    public function create(): Response
    {
        $categories = CategoryResource::collection(Category::all())->resolve();

        return inertia('Admin/Post/Create', compact('categories'));
    }

    public function store(StoreRequest $request)
    {
        //        dd($request->validationData());
        $data = $request->except('post.image');
        //        dd($data);
        $post = PostService::store($data);

        // обнуляем кеш постов в PostgreSQL, для редис и мемкешед есть tags и удаляется по другому
        DB::table('cache')->where('key', 'like', 'posts.%')->delete();

        return PostResource::make($post)->resolve();
    }

    public function update(UpdateRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $data = $request->except('post.image');
        $post = PostService::update($post, $data);

        // Загружаем необходимые отношения и подсчёты, чтобы избежать дополнительных запросов в ресурсах
        $post->load([
            'category',
            'profile.user',  // загружаем профиль вместе с пользователем для ProfileResource
            'tags',
            'likedProfiles',
            'parentComments', // если используете отношение для родительских комментариев
        ])->loadCount([
            'viewedProfiles',
            'parentComments', // если считаете родительские комментарии через loadCount
        ]);

        // обнуляем кеш постов в PostgreSQL, для редис и мемкешед есть tags и удаляется по другому
        DB::table('cache')->where('key', 'like', 'posts.%')->delete();

        return PostResource::make($post)->resolve();
    }

    public function destroy(Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        // обнуляем кеш постов в PostgreSQL, для редис и мемкешед есть tags и удаляется по другому
        DB::table('cache')->where('key', 'like', 'posts.%')->delete();

        return response()->json([
            'message' => 'The post was successfully deleted.',
        ], HttpResponse::HTTP_OK);
    }
}
