<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Role\IndexRequest;
use App\Http\Requests\Api\Admin\Role\StoreRequest;
use App\Http\Requests\Api\Admin\Role\UpdateRequest;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function index(IndexRequest $request)
    {
        $data = $request->validated();
        $roles = Role::filter($data)->get();
        return RoleResource::collection($roles)->resolve();
    }

    public function show(Role $role){

        return RoleResource::make($role)->resolve();

    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $role = Role::create($data);
        return RoleResource::make($role)->resolve();
    }

    public function update(Role $role, UpdateRequest $request)
    {
        $data = $request->validated();
        $role->update($data);
        return RoleResource::make($role)->resolve();
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response(['message' => 'Role has been deleted'], Response::HTTP_OK);
    }
}
