<?php

use App\Models\Project;
use App\Models\Setting;
use Database\Seeders\ProjectSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

/**
 * Content for the "It starts with a dot" redesign, applied to the live
 * database on deploy (the pipeline runs `migrate`, never the seeders).
 *
 * - Projects: adds Sajjah, Shattib and the two client sites, rewrites the
 *   earlier ones with accurate copy and the new showcase fields (matched by
 *   the English slug, so URLs do not change), and removes Tawazoun.
 * - Accent colour: the earlier dark theme used a lighter cyan tuned for dark
 *   backgrounds; the paper design uses the logo's own cyan. Only replaced if
 *   it still holds one of the old defaults.
 */
return new class extends Migration
{
    private const OLD_ACCENTS = ['#47bfe6', '#4f46e5'];

    public function up(): void
    {
        // The government project is no longer shown anywhere on the site.
        Project::query()->where('slug_en', 'tawazoun-land-eligibility-platform')->delete();

        foreach (ProjectSeeder::projects() as $project) {
            Project::updateOrCreate(['slug_en' => $project['slug_en']], $project);
        }

        $accent = Setting::query()->where('group', 'theme')->where('key', 'primary')->first();
        if ($accent && in_array(strtolower((string) $accent->value), self::OLD_ACCENTS, true)) {
            $accent->update(['value' => '#24abe3']);
        }

        Cache::forget('site:settings');
    }

    public function down(): void
    {
        // Content-only change; nothing to roll back structurally.
    }
};
