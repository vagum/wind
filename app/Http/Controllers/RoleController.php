<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return Role::all();
    }

    public function show(Role $role)
    {
        return $role;
    }

    public function store()
    {
        $roleData = [
            'title' => 'Some Role 1',
        ];

        return Role::create($roleData);
    }

    public function update(Role $role)
    {
        $roleData = [
            'title' => 'Some Role 1 Edit',
        ];

        $role->update($roleData);

        return $role;
    }

    public function destroy(Role $role)
    {

        $role->delete();

        return response(['message' => 'Role has been deleted']);
    }
}
