<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Indoor',
            'Outdoor',
            'Succulents',
            'Flowering',
            'Medicinal',
            'Gardening Essentials',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name], ['name' => $name]);
        }
    }
}
