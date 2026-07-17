<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProjectSeeder::class,
            ServiceSeeder::class,
            ExperienceSeeder::class,
            MetricSeeder::class,
            FocusItemSeeder::class,
            ProcessStepSeeder::class,
            TechnologySeeder::class,
            CapabilitySeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
