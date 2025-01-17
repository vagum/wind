<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Comment\StoreRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Http\Request;
use Inertia\Response;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all();
        $comments = CommentResource::collection($comments)->resolve();
        return inertia('Admin/Comment/Index', compact('comments'));
    }

    public function show(Comment $comment): Response
    {
        $comment = CommentResource::make($comment)->resolve();
        return inertia('Admin/Comment/Show', compact('comment'));
    }

    public function create(): Response
    {
        return inertia('Admin/Comment/Create');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['post_id'] = Post::inRandomOrder()->first()->id;
        $data['profile_id'] = Profile::inRandomOrder()->first()->id;
        $comment = Comment::create($data);
        return CommentResource::make($comment)->resolve();
    }
}
