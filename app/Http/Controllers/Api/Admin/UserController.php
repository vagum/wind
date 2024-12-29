<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\User\IndexRequest;
use App\Http\Requests\Api\Admin\User\StoreRequest;
use App\Http\Requests\Api\Admin\User\UpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(IndexRequest $request)
    {
        $data = $request->validated();
        $users = User::filter($data)->get();
        return UserResource::collection($users)->resolve();
    }

    public function show(User $user){

        return UserResource::make($user)->resolve();

    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);
        return UserResource::make($user)->resolve();
    }

    public function update(User $user, UpdateRequest $request)
    {
        $data = $request->validated();
        $user->update($data);
        return UserResource::make($user)->resolve();
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response(['message' => 'User has been deleted'], Response::HTTP_OK);
    }
}
