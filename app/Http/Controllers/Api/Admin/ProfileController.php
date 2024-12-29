<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Profile\IndexRequest;
use App\Http\Requests\Api\Admin\Profile\StoreRequest;
use App\Http\Requests\Api\Admin\Profile\UpdateRequest;
use App\Http\Resources\Profile\ProfileResource;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    public function index(IndexRequest $request)
    {
        $data = $request->validated();
        $profiles = Profile::filter($data)->get();
        return ProfileResource::collection($profiles)->resolve();
    }

    public function show(Profile $profile){

        return ProfileResource::make($profile)->resolve();

    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $profile = Profile::create($data);
        return ProfileResource::make($profile)->resolve();
    }

    public function update(Profile $profile, UpdateRequest $request)
    {
        $data = $request->validated();
        $profile->update($data);
        return ProfileResource::make($profile)->resolve();
    }

    public function destroy(Profile $profile)
    {
        $profile->delete();

        return response(['message' => 'Profile has been deleted'], Response::HTTP_OK);
    }
}
