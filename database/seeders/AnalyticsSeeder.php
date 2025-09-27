<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Analytics;
use Carbon\Carbon;

class AnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate sample data for the last 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Generate sessions (unique visitors)
            $sessionsCount = rand(15, 50);
            for ($j = 0; $j < $sessionsCount; $j++) {
                Analytics::create([
                    'type' => 'session',
                    'source' => 'frontend',
                    'page' => 'home',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'referrer' => 'https://google.com',
                    'metadata' => ['user_id' => null],
                    'created_at' => $date->copy()->addMinutes(rand(0, 1439))
                ]);
            }
            
            // Generate page views (more than sessions)
            $pageViewsCount = $sessionsCount * rand(2, 5);
            for ($j = 0; $j < $pageViewsCount; $j++) {
                $pages = ['home', 'about', 'programs', 'news', 'contact'];
                Analytics::create([
                    'type' => 'pageview',
                    'source' => 'frontend',
                    'page' => $pages[array_rand($pages)],
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'referrer' => 'https://google.com',
                    'metadata' => ['user_id' => null],
                    'created_at' => $date->copy()->addMinutes(rand(0, 1439))
                ]);
            }
            
            // Generate leads (form submissions, registrations)
            $leadsCount = rand(2, 8);
            for ($j = 0; $j < $leadsCount; $j++) {
                Analytics::create([
                    'type' => 'lead',
                    'source' => 'frontend',
                    'page' => 'registration',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'referrer' => 'https://google.com',
                    'metadata' => [
                        'form_type' => 'registration',
                        'user_id' => null
                    ],
                    'created_at' => $date->copy()->addMinutes(rand(0, 1439))
                ]);
            }
            
            // Generate conversions (program registrations, purchases)
            $conversionsCount = rand(1, 5);
            for ($j = 0; $j < $conversionsCount; $j++) {
                Analytics::create([
                    'type' => 'conversion',
                    'source' => 'frontend',
                    'page' => 'program-registration',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'referrer' => 'https://google.com',
                    'metadata' => [
                        'conversion_type' => 'program_registration',
                        'user_id' => null
                    ],
                    'created_at' => $date->copy()->addMinutes(rand(0, 1439))
                ]);
            }
        }
    }
}
