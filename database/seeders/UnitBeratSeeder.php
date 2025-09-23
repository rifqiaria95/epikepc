<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitBerat; // Import model UnitBerat

class UnitBeratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unitBerats = [
            ['nama' => 'Kilogram', 'simbol' => 'kg', 'konversi_ke_kg' => 1],
            ['nama' => 'Gram', 'simbol' => 'g', 'konversi_ke_kg' => 0.001],
            ['nama' => 'Miligram', 'simbol' => 'mg', 'konversi_ke_kg' => 0.000001],
            ['nama' => 'Ton', 'simbol' => 't', 'konversi_ke_kg' => 1000],
            ['nama' => 'Kuintal', 'simbol' => 'kw', 'konversi_ke_kg' => 100],
            ['nama' => 'Hektogram', 'simbol' => 'hg', 'konversi_ke_kg' => 0.1],
            ['nama' => 'Dekagram', 'simbol' => 'dag', 'konversi_ke_kg' => 0.01],
            ['nama' => 'Desigram', 'simbol' => 'dg', 'konversi_ke_kg' => 0.0001],
            ['nama' => 'Centigram', 'simbol' => 'cg', 'konversi_ke_kg' => 0.00001],
            ['nama' => 'Mikrogram', 'simbol' => 'µg', 'konversi_ke_kg' => 0.000000001],
            ['nama' => 'Pound', 'simbol' => 'lb', 'konversi_ke_kg' => 0.45359237],
            ['nama' => 'Ons', 'simbol' => 'oz', 'konversi_ke_kg' => 0.02834952],
            ['nama' => 'Kiloliter', 'simbol' => 'kl'],
            ['nama' => 'Hektoliter', 'simbol' => 'hl'],
            ['nama' => 'Dekaliter', 'simbol' => 'dal'],
            ['nama' => 'Liter', 'simbol' => 'l'],
            ['nama' => 'Desiliter', 'simbol' => 'dl'],
            ['nama' => 'Centiliter', 'simbol' => 'cl'],
            ['nama' => 'Mililiter', 'simbol' => 'ml'],
            ['nama' => 'Pcs', 'simbol' => 'pcs'],
        ];

        foreach ($unitBerats as $unitBerat) {
            UnitBerat::create($unitBerat);
        }
    }
}
