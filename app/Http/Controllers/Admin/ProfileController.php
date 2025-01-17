<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\StoreRequest;
use App\Http\Resources\Profile\ProfileResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::all();
        $profiles = ProfileResource::collection($profiles)->resolve();
        return inertia('Admin/Profile/Index', compact('profiles'));
    }

    public function show(Profile $profile): Response
    {
        $profile = ProfileResource::make($profile)->resolve();
        return inertia('Admin/Profile/Show', compact('profile'));
    }

    public function create(): Response
    {
        return inertia('Admin/Profile/Create');
    }

    public function store(StoreRequest $request)
    {
//        $data = $request->validated();
        $data = $request->except('image');
        $data['user_id'] = User::inRandomOrder()->first()->id;
        $profile = Profile::create($data);
        return ProfileResource::make($profile)->resolve();
    }
}
