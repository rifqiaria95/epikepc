<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            ['name' => 'Oli'],
            ['name' => 'Sparepart'],
            ['name' => 'Rem'],
            ['name' => 'Filter'],
            ['name' => 'Bahan Bakar'],
            ['name' => 'Lainnya'],
        ];

        foreach ($kategori as $k) {
            Kategori::create($k);
        }
    }
}
