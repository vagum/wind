<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Post\StoreRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::latest()->get();
        $posts = PostResource::collection($posts)->resolve();
        return inertia('Admin/Post/Index', compact('posts'));
    }

    public function show(Post $post): Response
    {
        $post = PostResource::make($post)->resolve();
        return inertia('Admin/Post/Show', compact('post'));
    }

    public function create(): Response
    {
        $categories = CategoryResource::collection(Category::all())->resolve();
        return inertia('Admin/Post/Create', compact('categories'));
    }

    public function store(StoreRequest $request)
    {
//        $data = $request->validated(); // не возвращает image при повторной валидации в реквесте
        $data = $request->except('image');
//        $data = $request->validationData(); // возвращает image по любому

//        dd($data);
        $data['profile_id'] = Profile::inRandomOrder()->first()->id;
//        unset($data['image']);
        $post = Post::create($data);
        return PostResource::make($post)->resolve();
    }
}
