<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Categories')->insert([
            'id' => 'HP',
            'name' => 'Hp',
            'created_at'=> '2020-10-10 10:10:10'
        ]);
        DB::table('Categories')->insert([
            'id' => 'LAPTOP',
            'name' => 'Laptop',
            'created_at'=> '2020-10-10 10:10:10'
        ]);
        DB::table('Categories')->insert([
            'id' => 'GUNTING',
            'name' => 'Gunting',
            'created_at'=> '2020-10-10 10:10:10'
        ]);
        DB::table('Categories')->insert([
            'id' => 'SISIR',
            'name' => 'Sisir',
            'created_at'=> '2020-10-10 10:10:10'
        ]);
    }
}
