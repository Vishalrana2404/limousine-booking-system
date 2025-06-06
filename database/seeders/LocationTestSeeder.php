<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks (optional, but useful if there are constraints)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table
        DB::table('locations')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert the data
        DB::table('locations')->insert([
            ['id' => 1, 'name' => 'Changi Airport Terminal 1', 'is_instant_acceptable' => true],
            ['id' => 2, 'name' => 'Changi Airport Terminal 2', 'is_instant_acceptable' => true],
            ['id' => 3, 'name' => 'Changi Airport Terminal 3', 'is_instant_acceptable' => true],
            ['id' => 4, 'name' => 'Changi Airport Terminal 4', 'is_instant_acceptable' => true],
            // ['id' => 5, 'name' => 'Changi Airport Jet Query', 'is_instant_acceptable' => false],
            // ['id' => 6, 'name' => 'Changi Airport VIP Complex', 'is_instant_acceptable' => false],
            ['id' => 7, 'name' => 'Seletar Airport', 'is_instant_acceptable' => true],
            ['id' => 8, 'name' => 'Woodlands Checkpoint', 'is_instant_acceptable' => true],
            ['id' => 9, 'name' => 'Tanah Merah Ferry Terminal', 'is_instant_acceptable' => true],
            ['id' => 10, 'name' => 'Singapore Cruise Centre', 'is_instant_acceptable' => true],
            ['id' => 11, 'name' => 'Marina Bay Cruise Centre', 'is_instant_acceptable' => true],
            ['id' => 12, 'name' => 'Others', 'is_instant_acceptable' => false],
        ]);
    }
}
