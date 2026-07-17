<?php

namespace Database\Seeders;

use App\Filament\Pages\AboutSettings;
use App\Filament\Pages\ContactSettings;
use App\Filament\Pages\HeroSettings;
use App\Filament\Pages\ImageSettings;
use App\Filament\Pages\RakeezSettings;
use App\Filament\Pages\SeoSettings;
use App\Filament\Pages\ServicesPageSettings;
use App\Filament\Pages\ThemeSettings;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Every settings group derives its defaults from the corresponding page,
     * so this seeder and the dashboard stay in sync automatically.
     */
    private const PAGES = [
        ThemeSettings::class,
        HeroSettings::class,
        AboutSettings::class,
        RakeezSettings::class,
        ServicesPageSettings::class,
        ContactSettings::class,
        SeoSettings::class,
        ImageSettings::class,
    ];

    public function run(): void
    {
        foreach (self::PAGES as $page) {
            $group = $page::group();

            foreach ($page::defaultValues() as $key => $value) {
                if ($value === null) {
                    continue; // image slots start empty and fall back to config.
                }

                Setting::updateOrCreate(
                    ['group' => $group, 'key' => $key],
                    ['value' => $value],
                );
            }
        }
    }
}
