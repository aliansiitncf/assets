<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::firstOrCreate([
            'name' => 'Laptop',
        ]);
        Category::firstOrCreate([
            'name' => 'PC',
        ]);
        Category::firstOrCreate([
            'name' => 'Printer',
        ]);
        Category::firstOrCreate([
            'name' => 'Scanner',
        ]);
        Category::firstOrCreate([
            'name' => 'Monitor',
        ]);
    }
}
