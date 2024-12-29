<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function show(Tag $tag)
    {
        return $tag;
    }

    public function store()
    {
        $tagData = [
            'title' => 'Some Tag 1',
        ];

        return Tag::create($tagData);
    }

    public function update(Tag $tag)
    {
        $tagData = [
            'title' => 'Some Tag 1 Edited',
        ];

        $tag->update($tagData);

        return $tag;
    }

    public function destroy(Tag $tag)
    {

        $tag->delete();

        return response(['message' => 'Tag has been deleted']);
    }
}
