<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sysAdmin = User::create([
            'username' => 'fares',
            'email' => 'test@example.com',
            'password' => Hash::make('123')
        ]);
        $sysAdmin->assignRole('System Admin');

        $admin = User::create([
            'username' => 'amr',
            'email' => 'test1@example.com',
            'password' => Hash::make('123')
        ]);
        $admin->assignRole('Admin');

        $user = User::create([
            'username' => 'ahmed',
            'email' => 'test2@example.com',
            'password' => Hash::make('123')
        ]);
        $user->assignRole('User');


    }
}
