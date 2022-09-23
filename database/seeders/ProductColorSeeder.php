<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_colors')->insert([
            'product_id' => 2,
            'color' => 'black',
            'price' => 58000,
            'image' => '/asset/img/product/67776650.jpeg'
        ]);
        DB::table('product_colors')->insert([
            'product_id' => 2,
            'color' => 'red',
            'price' => 58000,
            'image' => '/asset/img/product/201647307.jpg'
        ]);
        DB::table('product_colors')->insert([
            'product_id' => 2,
            'color' => 'blue',
            'price' => 58000,
            'image' => '/asset/img/product/1643726477.jpeg'
        ]);
        DB::table('product_colors')->insert([
            'product_id' => 2,
            'color' => 'gray',
            'price' => 58000,
            'image' => '/asset/img/product/1643726477.jpeg'
        ]);
        DB::table('product_colors')->insert([
            'product_id' => 2,
            'color' => 'pink',
            'price' => 58000,
            'image' => '/asset/img/product/1643726477.jpeg'
        ]);
    }
}
