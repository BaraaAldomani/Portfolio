<?php

use App\Models\Experience;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

/**
 * One-time content update for the live database (deploys run `migrate`).
 *
 * - Rakeez is presented as a client, not a company Baraa founded: drop the
 *   "Founder · Rakeez" experience and the retired founder-block settings, and
 *   reword the "Where I have worked" lead that mentioned founding a studio.
 * - Fix the "الضفط" typo in the Arabic hero lead.
 *
 * Every change only touches the exact seeded value, so anything already
 * edited by hand in the dashboard is left alone.
 */
return new class extends Migration
{
    private const WORK_LEAD = [
        'ar' => [
            'old' => 'من الهندسة العملية داخل فِرق المنتجات إلى تأسيس وقيادة الاستوديو الخاص بي.',
            'new' => 'من تحليل الأنظمة إلى قيادة فرق الواجهة الخلفية وبناء أنظمة على نطاق وطني.',
        ],
        'en' => [
            'old' => 'From hands-on engineering in product teams to founding and leading my own studio.',
            'new' => 'From systems analysis to leading backend teams and building national-scale systems.',
        ],
    ];

    public function up(): void
    {
        Experience::query()
            ->where('org_en', 'Rakeez')
            ->where('url', 'like', '%rakeez-llc.com%')
            ->get()
            ->each->delete();

        Setting::query()->where('group', 'rakeez')->delete();
        Setting::query()->where('group', 'images')->where('key', 'rakeez_logo')->delete();

        foreach (self::WORK_LEAD as $locale => $copy) {
            $this->replaceSetting('about', "work_lead_{$locale}", fn (string $v) => $v === $copy['old'] ? $copy['new'] : $v);
        }

        $this->replaceSetting('home', 'hero_lead_ar', fn (string $v) => str_replace('الضفط', 'الضغط', $v));

        Cache::forget('site:settings');
    }

    public function down(): void
    {
        // Content-only change; nothing to roll back structurally.
    }

    /**
     * @param  callable(string): string  $transform
     */
    private function replaceSetting(string $group, string $key, callable $transform): void
    {
        $setting = Setting::query()->where('group', $group)->where('key', $key)->first();

        if (! $setting || ! is_string($setting->value)) {
            return;
        }

        $updated = $transform($setting->value);

        if ($updated !== $setting->value) {
            $setting->update(['value' => $updated]);
        }
    }
};
