<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the roles and permissions seeder
        $this->call(RolesAndPermissionsSeeder::class);

        // Call the sample data seeder
        $this->call(SampleDataSeeder::class);
    }
}
