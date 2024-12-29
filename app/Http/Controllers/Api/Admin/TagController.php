<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Tag\IndexRequest;
use App\Http\Requests\Api\Admin\Tag\StoreRequest;
use App\Http\Requests\Api\Admin\Tag\UpdateRequest;
use App\Http\Resources\Tag\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(IndexRequest $request)
    {
        $data = $request->validated();
        $tags = Tag::filter($data)->get();
        return TagResource::collection($tags)->resolve();
    }

    public function show(Tag $tag){

        return TagResource::make($tag)->resolve();

    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $tag = Tag::create($data);
        return TagResource::make($tag)->resolve();
    }

    public function update(Tag $tag, UpdateRequest $request)
    {
        $data = $request->validated();
        $tag->update($data);
        return TagResource::make($tag)->resolve();
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response(['message' => 'Tag has been deleted'], Response::HTTP_OK);
    }
}
