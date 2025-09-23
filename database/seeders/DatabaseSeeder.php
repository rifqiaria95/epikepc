<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UnitBerat;
use App\Models\MenuDetail;
use App\Models\MenuGroup;
use App\Models\Vendor;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\VillagesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProvincesSeeder::class,
            CitiesSeeder::class,
            DistrictsSeeder::class,
            VillagesSeeder::class,
            MenuSeeder::class,
            UnitBeratSeeder::class,
            RolePermissionSeeder::class,
            VendorSeeder::class,
            UserSeeder::class,
            NewsSeeder::class,
            TestimoniSeeder::class,
        ]);

        Pelanggan::factory(10)->create();
        User::factory(5)->create();


    }
}
