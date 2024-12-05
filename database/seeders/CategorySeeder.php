<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Medical Machine',
                'description' => ' ',
            ],
            [
                'name' => 'Medical Equipment',
                'description' => ' ',
            ],
            [
                'name' => 'Medical Products',
                'description' => ' ',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 