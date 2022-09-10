<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('images')->insert([
            'image' => '/asset/img/product/697923761.jpeg',
            'category' => 'product',
            'product_id' => '1'
        ]);

        DB::table('images')->insert([
            'image' => '/asset/img/product/67776650.jpeg',
            'category' => 'product',
            'product_id' => '2'
        ]);

        DB::table('images')->insert([
            'image' => '/asset/img/product/201647307.jpeg',
            'category' => 'product',
            'product_id' => '3'
        ]);

        DB::table('images')->insert([
            'image' => '/asset/img/product/24597262.jpeg',
            'category' => 'product',
            'product_id' => '4'
        ]);

        DB::table('images')->insert([
            'image' => '/asset/img/product/1073520210.jpeg',
            'category' => 'product',
            'product_id' => '5'
        ]);

        DB::table('images')->insert([
            'image' => '/asset/img/product/1724398582.jpeg',
            'category' => 'product',
            'product_id' => '6'
        ]);

        DB::table('images')->insert([
            'image' => '/asset/img/product/1415216747.jpeg',
            'category' => 'product',
            'product_id' => '7'
        ]);

        DB::table('images')->insert([
            'image' => '/asset/img/product/1518549433.jpeg',
            'category' => 'product',
            'product_id' => '8'
        ]);

        DB::table('images')->insert([
            'image' => '/asset/img/product/1643726477.jpeg',
            'category' => 'product',
            'product_id' => '9'
        ]);
    }
}
