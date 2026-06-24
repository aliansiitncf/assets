<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::firstOrCreate([
            'name' => 'IT Support',
        ]);
        Location::firstOrCreate([
            'name' => 'HRD',
        ]);
        Location::firstOrCreate([
            'name' => 'Accounting',
        ]);
        Location::firstOrCreate([
            'name' => 'General Affair',
        ]);
    }
}
