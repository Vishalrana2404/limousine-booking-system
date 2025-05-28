<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // Truncate the UserType table
        UserType::truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $userTypes = config('constants.userTypes');
        foreach ($userTypes as $userType) {
            UserType::updateOrCreate(
                ['name' => $userType['name'], 'type' => $userType['type'], 'slug' => $userType['slug']],
                $userType
            );
        }
    }
}
