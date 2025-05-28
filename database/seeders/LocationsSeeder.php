<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('locations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $locations = config('constants.locations');
        foreach ($locations as $data) {
            Location::updateOrCreate(
                ['id' => $data['id']],
                ['name' => $data['name'], 'is_instant_acceptable' => $data['is_instant_acceptable']]
            );
        }
    }
}
