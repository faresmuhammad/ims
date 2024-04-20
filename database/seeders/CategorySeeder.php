<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Tablets & Capsules',
            'Syrups',
            'Injection',
            'Drops',
            'Sprays & Inhalers',
            'Multivitamins',
            'Woman Care',
            'Cosmetics',
            'Baby Care',
            'Mouth Care',
            'Creams & Ointments',
            'Effervescent Powder',
            'Milks',
            'Weight Control'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category)
            ]);
        }
    }
}
