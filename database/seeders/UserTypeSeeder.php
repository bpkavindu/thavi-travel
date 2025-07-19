<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('user_types')->insert([
            ['name' => 'admin', 'show_global_form' => '0'],
            ['name' => 'Traveller', 'show_global_form' => '1'],
            ['name' => 'Property', 'show_global_form' => '1'],
            ['name' => 'Tour Guid', 'show_global_form' => '1'],
        ]);
    }
}
