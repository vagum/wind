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
use App\Models\Profile;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PostController extends Controller
{
    public function index(IndexRequest $request): AnonymousResourceCollection|Response
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
        return inertia('Admin/Post/Index', [
            'posts'          => $posts,
            'active_filters' => $active_filters,
        ]);
    }

    public function show(Post $post): Response
    {
        // Eager load comments и профили с пользователями
        $post->load(['comments.profile.user']);

        // Преобразование поста в ресурс
        $postResource = PostResource::make($post)->resolve();

        // Возвращение данных на фронтенд через Inertia
        return Inertia::render('Admin/Post/Show', [
            'post' => $postResource,
        ]);

//        $post = PostResource::make($post)->resolve();
//        return inertia('Admin/Post/Show', compact('post'));
    }

    public function edit(Post $post): Response
    {
        $post = PostResource::make($post)->resolve();
        $categories = CategoryResource::collection(Category::all())->resolve();
        return inertia('Admin/Post/Edit', compact('post','categories'));
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
        return PostResource::make($post)->resolve();
    }

    public function update(UpdateRequest $request, Post $post)
    {
//        dd($request->validationData());
        $data = $request->except('post.image');
//        dd($data);
        $post = PostService::update($post, $data);
        return PostResource::make($post)->resolve();
    }

    public function destroy(Post  $post): JsonResponse
    {
        $post->delete();
        return response()->json([
            'message' => 'The post was successfully deleted.',
        ], HttpResponse::HTTP_OK);
    }
}
