<?php

namespace Database\Seeders;

use App\Models\Pricing;
use App\Models\PricingFeature;
use App\Models\User;
use Illuminate\Database\Seeder;

class PricingSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');

        if (! $userId) {
            $this->command?->warn('PricingSeeder skipped: no users found.');

            return;
        }

        $promoFeatures = [
            'Promo Hemat 1: Bayar 3 bulan, gratis 1 bulan',
            'Promo Hemat 2: Bayar 6 bulan, gratis 2 bulan',
            'Promo Hemat 3: Bayar 12 bulan, gratis 3 bulan',
            '*Syarat & ketentuan berlaku',
        ];

        $commonFeatures = [
            'Gratis Pemasangan',
            'Biaya Bulanan Flat',
            'Jaringan Fiber Optik (FO)',
            'Unlimited Tanpa Batas Kuota',
            'Service 24/7',
            'Harga Sudah Termasuk PPN',
        ];

        $plans = [
            [
                'name' => 'Paket Ekonomis',
                'price' => 150000,
                'billing_period' => 'month',
                'description' => 'Internet hemat untuk kebutuhan dasar rumah tangga.',
                'is_popular' => false,
                'sort_order' => 1,
                'features' => array_merge(
                    ['Kecepatan hingga 50 Mbps'],
                    $commonFeatures,
                    $promoFeatures
                ),
            ],
            [
                'name' => 'Paket Basic',
                'price' => 180000,
                'billing_period' => 'month',
                'description' => 'Paket seimbang untuk browsing, streaming, dan aktivitas harian.',
                'is_popular' => false,
                'sort_order' => 2,
                'features' => array_merge(
                    ['Kecepatan hingga 70 Mbps'],
                    $commonFeatures,
                    $promoFeatures
                ),
            ],
            [
                'name' => 'Paket Reguler',
                'price' => 225000,
                'billing_period' => 'month',
                'description' => 'Pilihan populer untuk keluarga dan pekerjaan hybrid.',
                'is_popular' => true,
                'sort_order' => 3,
                'features' => array_merge(
                    ['Kecepatan hingga 100 Mbps'],
                    $commonFeatures,
                    $promoFeatures
                ),
            ],
            [
                'name' => 'Paket Ngebut',
                'price' => 300000,
                'billing_period' => 'month',
                'description' => 'Kecepatan maksimal untuk gaming, meeting online, dan upload berat.',
                'is_popular' => false,
                'sort_order' => 4,
                'features' => array_merge(
                    ['Kecepatan hingga 150 Mbps'],
                    $commonFeatures,
                    $promoFeatures
                ),
            ],
            [
                'name' => 'Paket Hemat 1',
                'price' => 450000,
                'billing_period' => 'month',
                'description' => 'Bayar 3 bulan, gratis 1 bulan. Berlaku untuk semua paket internet SFX NET.',
                'is_popular' => false,
                'sort_order' => 5,
                'is_active' => false,
                'features' => [
                    'Bayar 3 bulan di muka',
                    'Bonus gratis 1 bulan',
                    'Total mendapat 4 bulan layanan',
                    'Berlaku untuk semua paket internet',
                    '*Syarat & ketentuan berlaku',
                ],
            ],
            [
                'name' => 'Paket Hemat 2',
                'price' => 900000,
                'billing_period' => 'month',
                'description' => 'Bayar 6 bulan, gratis 2 bulan. Berlaku untuk semua paket internet SFX NET.',
                'is_popular' => false,
                'sort_order' => 6,
                'is_active' => false,
                'features' => [
                    'Bayar 6 bulan di muka',
                    'Bonus gratis 2 bulan',
                    'Total mendapat 8 bulan layanan',
                    'Berlaku untuk semua paket internet',
                    '*Syarat & ketentuan berlaku',
                ],
            ],
            [
                'name' => 'Paket Hemat 3',
                'price' => 1800000,
                'billing_period' => 'month',
                'description' => 'Bayar 12 bulan, gratis 3 bulan. Berlaku untuk semua paket internet SFX NET.',
                'is_popular' => false,
                'sort_order' => 7,
                'is_active' => false,
                'features' => [
                    'Bayar 12 bulan di muka',
                    'Bonus gratis 3 bulan',
                    'Total mendapat 15 bulan layanan',
                    'Berlaku untuk semua paket internet',
                    '*Syarat & ketentuan berlaku',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            $features = $plan['features'];
            unset($plan['features']);

            $pricing = Pricing::updateOrCreate(
                ['name' => $plan['name']],
                [
                    ...$plan,
                    'is_active' => $plan['is_active'] ?? true,
                    'button_url' => '/contact',
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            PricingFeature::where('pricing_id', $pricing->id)->forceDelete();

            foreach ($features as $index => $feature) {
                PricingFeature::create([
                    'pricing_id' => $pricing->id,
                    'feature' => $feature,
                    'sort_order' => $index,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        $seededNames = array_column($plans, 'name');

        Pricing::query()
            ->whereNotIn('name', $seededNames)
            ->update([
                'is_active' => false,
                'updated_by' => $userId,
            ]);

        $this->command?->info('Pricing data SFX NET seeded successfully!');
    }
}
