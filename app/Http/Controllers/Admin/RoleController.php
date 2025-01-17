<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRequest;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Response;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $roles = RoleResource::collection($roles)->resolve();
        return inertia('Admin/Role/Index', compact('roles'));
    }

    public function show(Role $role): Response
    {
        $role = RoleResource::make($role)->resolve();
        return inertia('Admin/Role/Show', compact('role'));
    }

    public function create(): Response
    {
        return inertia('Admin/Role/Create');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $role = Role::create($data);
        return RoleResource::make($role)->resolve();
    }
}
