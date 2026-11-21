<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

/**
 * The LinkedIn link on the site (footer, structured data, llms.txt) pointed to
 * /in/baraa-aldomani/, which LinkedIn answers with "page not found". The
 * profile is at /in/baraaaldomani/.
 *
 * The link lives in the settings table on the server, so it is corrected
 * there, and only if it still holds the wrong address: a link the owner has
 * changed in the dashboard is left alone.
 */
return new class extends Migration
{
    private const WRONG = 'https://www.linkedin.com/in/baraa-aldomani';

    private const RIGHT = 'https://www.linkedin.com/in/baraaaldomani/';

    public function up(): void
    {
        Setting::query()
            ->where('group', 'contact')
            ->where('key', 'linkedin')
            ->get()
            ->filter(fn (Setting $setting) => is_string($setting->value) && rtrim($setting->value, '/') === self::WRONG)
            ->each(fn (Setting $setting) => $setting->update(['value' => self::RIGHT]));

        Cache::forget('site:settings');
    }

    public function down(): void
    {
        // The old address never worked; nothing to go back to.
    }
};
