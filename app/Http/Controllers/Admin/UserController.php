<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\User\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $users = UserResource::collection($users)->resolve();
        return inertia('Admin/User/Index', compact('users'));
    }

    public function show(User $user): Response
    {
        $user = UserResource::make($user)->resolve();
        return inertia('Admin/User/Show', compact('user'));
    }

    public function create(): Response
    {
        $roles = RoleResource::collection(Role::all())->resolve();
        return inertia('Admin/User/Create', compact('roles'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        // Проверяем наличие поля role_id
        if (empty($data['role_id'])) {
            return response()->json([
                'error' => 'Поле role_id обязательно для передачи.'
            ], 422);
        }

        // Ищем роль по переданному идентификатору.
        $role = Role::find($data['role_id']);
        if (!$role) {
            return response()->json([
                'error' => 'Указанная роль не найдена.'
            ], 404);
        }

        // Убираем role_id из данных, чтобы не записывать его в таблицу users
        unset($data['role_id']);

        // Создаём пользователя
        $user = User::create($data);

        // Привязываем найденную роль к пользователю через отношение many-to-many
        $user->roles()->attach($role->id);

        return UserResource::make($user)->resolve();
    }

}
