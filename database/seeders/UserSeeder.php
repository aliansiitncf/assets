<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'administrator')->first();
        $userRole = Role::where('name', 'user')->first();

        $user1 = User::firstOrCreate([
            'name' => 'Irgo Satya',
            'email' => 'irgosg@gmail.com',
            'password' => Hash::make('admin123'),
        ]);
        $user1->syncRoles([$adminRole->id]);

        $user2 = User::firstOrCreate([
            'name' => 'Nasywa',
            'email' => 'nasywa@gmail.com',
            'password' => Hash::make('admin123'),
        ]);
        $user2->syncRoles([$userRole->id]);
    }
}
