<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $role = Role::create([
            'role_name' => 'admin'
        ]);

        User::create([
            'name' => 'Admin test',
            'email' => 'admin@email.com',
            'password' => Hash::make('password'),
            'phone' => '000011112222',
            'role_id' => $role->id,
        ]);
    }
}
