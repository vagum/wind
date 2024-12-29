<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show(User $user)
    {
        return $user;
    }

    public function store()
    {
        $userData = [
            'name' => 'Vasia',
            'email' => 'vasia@gmail.com',
            'password' => '1234567',
        ];

        return User::create($userData);
    }

    public function update(User $user)
    {
        $userData = [
            'name' => 'Vasia edited',
            'email' => 'vasia_edited@gmail.com',
        ];

        $user->update($userData);

        return $user;
    }

    public function destroy(User $user)
    {

        $user->delete();

        return response(['message' => 'User has been deleted']);
    }
}
