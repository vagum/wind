<?php

namespace Database\Seeders;

use App\Models\Role;
use Database\Factories\RoleFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Role::getRoles() as $key => $roleTitle) {
            Role::firstOrCreate([
                'title' => $roleTitle
            ],[
                'idx' => $key
            ]);
        }

        foreach (RoleFactory::generateModeratorRoles() as $role) {
            Role::create($role);
        }
    }
}
