<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotoSpotSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('photo_spots')->insert([
            [
                'name' => 'Sigiriya Rock Fortress',
                'location' => 'Sigiriya, Sri Lanka',
                'description' => 'An ancient rock fortress and one of Sri Lanka’s most iconic landmarks.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nine Arch Bridge',
                'location' => 'Ella, Sri Lanka',
                'description' => 'Famous stone bridge in the misty hills of Ella.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
