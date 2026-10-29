<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['category' => 'Batik Tulis', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Batik Cap', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Batik Kombinasi', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}