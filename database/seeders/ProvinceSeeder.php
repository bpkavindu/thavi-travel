<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
        $province = [
            'Western Province',
            'Central Province',
            'Southern Province',
            'Eastern Province',
            'Northern Province',
            'North Western Province',
            'North Central Province',
            'Sabaragamuwa Province',
            'Uva Province'
        ];

        foreach ($province as $name) {
            Province::create(['name' => $name]);
        }
    }
}
