<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function show(Post $post)
    {
        return $post;
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
}

