<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'category' => 'Pakaian Pria',
            'image' => '/asset/img/category/pakaian-pria.png',
            'type' => 'man'
        ]);

        DB::table('categories')->insert([
            'category' => 'Tas Pria',
            'image' => '/asset/img/category/tas-pria.png',
            'type' => 'man'
        ]);

        DB::table('categories')->insert([
            'category' => 'Sepatu Pria',
            'image' => '/asset/img/category/sepatu-pria.png',
            'type' => 'man'
        ]);

        DB::table('categories')->insert([
            'category' => 'Pakaian Wanita',
            'image' => '/asset/img/category/pakaian-wanita.png',
            'type' => 'woman'
        ]);

        DB::table('categories')->insert([
            'category' => 'Tas Wanita',
            'image' => '/asset/img/category/tas-wanita.png',
            'type' => 'woman'
        ]);

        DB::table('categories')->insert([
            'category' => 'Sepatu Wanita',
            'image' => '/asset/img/category/sepatu-wanita.png',
            'type' => 'woman'
        ]);

        DB::table('categories')->insert([
            'category' => 'Fashion Muslim',
            'image' => '/asset/img/category/fashion-muslim.png',
            'type' => 'woman'
        ]);

        DB::table('categories')->insert([
            'category' => 'Jam Tangan',
            'image' => '/asset/img/category/jam-tangan.png',
            'type' => 'all'
        ]);

        DB::table('categories')->insert([
            'category' => 'Aksesoris Fashion',
            'image' => '/asset/img/category/aksesoris.png',
            'type' => 'all'
        ]);
    }
}
