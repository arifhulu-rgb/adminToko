<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Batik Parang',
                'description' => 'Kain batik motif parang khas Nusantara.',
                'category_id' => 1,
                'price' => 150000,
                'img' => 'batik-parang.jpg',
                'stock' => 10,
                'slug' => 'batik-parang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Batik Kawung',
                'description' => 'Kain batik motif kawung klasik.',
                'category_id' => 2,
                'price' => 175000,
                'img' => 'batik-kawung.jpg',
                'stock' => 8,
                'slug' => 'batik-kawung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}