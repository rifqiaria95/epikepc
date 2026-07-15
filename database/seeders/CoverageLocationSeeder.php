<?php

namespace Database\Seeders;

use App\Models\CoverageLocation;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoverageLocationSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');

        $areas = [
            'Boyolali' => [
                'Sawahan' => [
                    'dukuh' => ['Mojorejo', 'Mojoasri', 'Sadon Byl', 'Klajiran', 'Dukuh Rejo', 'Meletan', 'Kedunggupit', 'Ledok', 'Karang Mojo', 'Padokan'],
                    'perumahan' => ['Pamela Sadon', 'Mirajati 1 Kedunggupit', 'Miraijati 3 Ledok', 'Perum Bintang Ledok', 'Peruk Pandawa Ledok', 'Perum Abyyasa Sadon', 'Perum Welar', 'Perum Gardenia', 'Perum Asri Kedungmasan/Domasan', 'Perum Meletan Village'],
                ],
                'Pandeyan' => [
                    'dukuh' => ['Welar', 'Pandeyan', 'Kurukan', 'Garen', 'Menjing'],
                ],
                'Kismoyoso' => [
                    'dukuh' => ['Tambas', 'Kedungdowo', 'Kismoyoso', 'Kedungmasan/Domasan'],
                ],
            ],
            'Karanganyar' => [
                'Wonorejo' => [
                    'dukuh' => ['Sadon Wonorejo', 'Wonoharjo', 'Sugihwaras', 'Jetak', 'Selorejo', 'Sukuh Agung'],
                ],
                'Selokaton' => [
                    'dukuh' => ['Selokaton', 'Mundu', 'Pancuran', 'Ngagglik', 'Tegalsari'],
                    'perumahan' => ['Perum GMS', 'Perum GSI', 'Perum Pondok Bukhari'],
                ],
                'Jatikuwung' => [
                    'dukuh' => ['Jatikuwung', 'Terek', 'Winong', 'Kleco', 'Wonosari'],
                    'perumahan' => ['Perum Jatikuwung Asri', 'Perum Jatikuwung Asri 2/New'],
                ],
                'Jeruk Sawit' => [
                    'dukuh' => ['Banyuanyar', 'Jatisari'],
                ],
            ],
            'Surakarta' => [
                'Kadipiro' => [
                    'dukuh' => ['Ngipang', 'Lemah Abang', 'Bayan', 'Sruni', 'Plelen', 'Kadipiro', 'Combong', 'Kleco', 'Kalingga', 'Krembyongan/Rembyongan', 'Sukomulyo'],
                ],
                'Nusukan' => [
                    'dukuh' => ['Prawit', 'Minapadi'],
                ],
            ],
        ];

        $sortOrder = 1;

        foreach ($areas as $kabupaten => $kelurahanList) {
            foreach ($kelurahanList as $kelurahan => $types) {
                foreach ($types as $type => $locations) {
                    foreach ($locations as $name) {
                        CoverageLocation::updateOrCreate(
                            [
                                'kabupaten' => $kabupaten,
                                'kelurahan' => $kelurahan,
                                'name' => $name,
                                'type' => $type,
                            ],
                            [
                                'is_active' => true,
                                'sort_order' => $sortOrder++,
                                'created_by' => $userId,
                                'updated_by' => $userId,
                            ]
                        );
                    }
                }
            }
        }

        $referencePlaces = [
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Teamur',
            'Surakarta', 'Boyolali', 'Karanganyar', 'Semarang', 'Yogyakarta', 'Solo', 'Salatiga',
            'Magelang', 'Klaten', 'Sukoharjo', 'Wonogiri', 'Sragen',
        ];

        $referenceOrder = 1;

        foreach ($referencePlaces as $place) {
            CoverageLocation::updateOrCreate(
                [
                    'name' => $place,
                    'type' => CoverageLocation::TYPE_REFERENCE,
                    'kabupaten' => null,
                    'kelurahan' => null,
                ],
                [
                    'is_active' => true,
                    'sort_order' => $referenceOrder++,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        CoverageLocation::clearCache();
    }
}
