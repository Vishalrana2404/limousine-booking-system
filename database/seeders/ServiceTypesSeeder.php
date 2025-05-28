<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = config('constants.serviceTypes');
        foreach ($serviceTypes as $serviceType) {
            ServiceType::updateOrCreate(
                ['id' => $serviceType['id']],
                ['name' => $serviceType['name']]
            );
        }
    }
}
