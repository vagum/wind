<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tag\StoreRequest;
use App\Http\Resources\Tag\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Response;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        $tags = TagResource::collection($tags)->resolve();
        return inertia('Admin/Tag/Index', compact('tags'));
    }

    public function show(Tag $tag): Response
    {
        $tag = TagResource::make($tag)->resolve();
        return inertia('Admin/Tag/Show', compact('tag'));
    }

    public function create(): Response
    {
        return inertia('Admin/Tag/Create');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $tag = Tag::create($data);
        return TagResource::make($tag)->resolve();
    }
}
