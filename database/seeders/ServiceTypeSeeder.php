<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');

        if (! $userId) {
            $this->command?->warn('ServiceTypeSeeder skipped: no users found.');

            return;
        }

        $types = [
            ['name' => 'EPC Services', 'slug' => 'epc-services', 'type' => 'it'],
            ['name' => 'Operation & Maintenance', 'slug' => 'operation-maintenance', 'type' => 'it'],
            ['name' => 'Agencies & Trading', 'slug' => 'agencies-trading', 'type' => 'it'],
            ['name' => 'Logistic Transportation', 'slug' => 'logistic-transportation', 'type' => 'it'],
            ['name' => 'Investment & Capital', 'slug' => 'investment-capital', 'type' => 'it'],
        ];

        foreach ($types as $type) {
            ServiceType::updateOrCreate(
                ['slug' => $type['slug']],
                [
                    'name'       => $type['name'],
                    'type'       => $type['type'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        ServiceType::query()
            ->whereNotIn('slug', array_column($types, 'slug'))
            ->delete();

        $this->command?->info('EPIK service types seeded successfully!');
    }
}
