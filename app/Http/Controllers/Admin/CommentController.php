<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Comment\StoreRequest;
use App\Http\Requests\Admin\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CommentController extends Controller
{
    public function index()
    {
        $profileId = auth()->check() && auth()->user()->profile
            ? auth()->user()->profile->id
            : null;

        $comments = Comment::with([
            'post',
            'profile',        // Если в ProfileResource уже настроен eager loading для user (через $with или with(['user']))
            'profile.user',
            'replies'
        ])
            ->withCount('likedProfiles')
            ->withCount('replies')
            // Добавляем withExists для проверки, лайкнул ли текущий пользователь
            ->withExists(['likedProfiles as is_liked' => function ($query) use ($profileId) {
                if ($profileId) {
                    $query->where('profiles.id', $profileId);
                }
            }])
            ->get();

        return inertia('Admin/Comment/Index', [
            'comments' => CommentResource::collection($comments)->resolve(),
        ]);
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

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        Gate::authorize('update', $comment);
        $data = $request->validated();
//        dd($request->validationData());
//        dd($data);
        $comment->update($data);

        // Возвращаем обновлённый комментарий
        return response()->json($comment);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);
        $comment->delete();
        return response()->json([
            'message' => 'The comment was successfully deleted.',
        ], HttpResponse::HTTP_OK);
    }
}
