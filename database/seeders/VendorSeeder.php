<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        $provinceIds = DB::table('indonesia_provinces')
        ->whereBetween('id_provinsi', [1, 34])
        ->pluck('id_provinsi')
        ->toArray();

        $cityIds = DB::table('indonesia_cities')
        ->whereBetween('id_kota', [500, 800])
        ->pluck('id_kota')
        ->toArray();

        for ($i = 0; $i < 50; $i++) {
            DB::table('vendor')->insert([
                'nm_vendor'      => $faker->company,
                'alamat_vendor'  => $faker->address,
                'no_telp_vendor' => $faker->phoneNumber,
                'id_kota'        => $faker->randomElement($cityIds),
                'id_provinsi'    => $faker->randomElement($provinceIds),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
