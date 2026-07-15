<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Laravolt\Indonesia\Seeds\VillagesSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProvincesSeeder::class,
            CitiesSeeder::class,
            DistrictsSeeder::class,
            VillagesSeeder::class,
            MenuSeeder::class,
            RolePermissionSeeder::class,
            CmsPermissionSeeder::class,
            UserSeeder::class,
            PricingSeeder::class,
            ServiceTypeSeeder::class,
            AboutSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            CompanyJourneySeeder::class,
            EpikComproSeeder::class,
            ConsultationPermissionSeeder::class,
            CoveragePermissionSeeder::class,
            CoverageLocationSeeder::class,
        ]);
    }
}
