<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\IndexRequest;
use App\Http\Requests\Post\StoreCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
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

        return CommentResource::make($comment)->resolve();
    }

    public function toggleLike(Post $post): JsonResponse
    {
        // Переключаем лайк для текущего пользователя
        $res = $post->likedProfiles()->toggle(auth()->user()->profile->id);

        // Определяем, добавлен или удален лайк
        $isLiked = count($res['attached']) > 0;

        // Получаем обновленное количество лайков
        $likesCount = $post->likedProfiles()->count();

        // Получаем количество комментариев
        $commentsCount = $post->comments()->count();

        return response()->json([
            'is_liked' => $isLiked,
            'likes' => $likesCount,
            'comments_count' => $commentsCount,
        ]);

    }
}

