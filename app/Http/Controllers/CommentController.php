<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index()
    {
        return Comment::all();
    }

    public function show(Comment $comment)
    {
        return $comment;
    }

    public function store()
    {
        $commentData =[
            'author' => 'Petia',
            'content' => 'Some Comment 1',
            'post_id' => 1,
        ];

        return Comment::create($commentData);
    }

    public function update(Comment $comment)
    {
        $commentData =[
            'author' => 'Petia Edited',
            'content' => 'Some Comment 1 Edited',
        ];

        $comment->update($commentData);

        return $comment;
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response(['message' => 'Comment has been deleted']);
    }
}
