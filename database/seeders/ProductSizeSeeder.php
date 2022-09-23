<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_sizes')->insert([
            'product_id' => 2,
            'size' => 'S',
            'price' => 57500,
        ]);
        DB::table('product_sizes')->insert([
            'product_id' => 2,
            'size' => 'M',
            'price' => 57500,
        ]);
        DB::table('product_sizes')->insert([
            'product_id' => 2,
            'size' => 'L',
            'price' => 58000,
        ]);
        DB::table('product_sizes')->insert([
            'product_id' => 2,
            'size' => 'XL',
            'price' => 59500,
        ]);
        DB::table('product_sizes')->insert([
            'product_id' => 2,
            'size' => 'XXL',
            'price' => 60000,
        ]);
    }
}
