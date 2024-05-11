<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::create([
            'username' => 'fares',
            'email' => 'test@example.com',
            'password' => Hash::make('123')
        ]);

        $this->call(
            [
                CategorySeeder::class,
                ProductSeeder::class,
                SupplierSeeder::class
            ]
        );
    }
}
