<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

/**
 * Page descriptions for search and sharing, brought in line with the
 * redesigned site: written for founders, prices are public, and the
 * government project is no longer mentioned.
 *
 * These live in the settings table on the server (the dashboard owns them),
 * so the lang files alone would not change the live site. Each value is
 * replaced only if it still holds the old default: anything the owner has
 * edited in the dashboard is left alone.
 */
return new class extends Migration
{
    /** key => [old default, new text] */
    private const CHANGES = [
        "home_description_ar" => ["مبرمج ومطوّر مواقع مستقل في الرياض. أبني تطبيقات ويب وجوال وأنظمة إدارة ومواقع احترافية وواجهات API لعملاء في السعودية. حوّل فكرتك إلى منتج يعمل، استشارة أولى مجانية.", "مهندس برمجيات مستقل في الرياض لروّاد الأعمال وأصحاب المشاريع. أبني موقعك أو منصتك أو تطبيقك من الفكرة حتى الإطلاق، بنطاق مكتوب وسعر معلن. المكالمة الأولى مجانية."],
        "services_description_ar" => ["تطوير أنظمة مخصصة وتطبيقات ويب وجوال ومواقع احترافية وتكاملات وواجهات API، من مهندس برمجيات مستقل في الرياض.", "ستة أنواع من المشاريع بأسعار بداية معلنة ومدة واضحة: مواقع، متاجر ومنصات حجوزات، أنظمة إدارة، تطبيقات جوال، تكاملات API، وأتمتة بالذكاء الاصطناعي. من مهندس برمجيات مستقل في الرياض."],
        "projects_description_ar" => ["مشاريع منفّذة بمنهجية واضحة: التحدي، الحل، والنتيجة. أنظمة وتطبيقات ومنصات بنيتُها لعملاء في قطاعات الحكومة والصحة والتعليم والتجارة.", "مشاريع حقيقية تعمل الآن: منصة حجز ديوانيات، منصة تشطيب وترميم، ومواقع لأصحاب مهن مستقلين. لكل مشروع قصته: التحدي، الحل، والنتيجة."],
        "about_description_ar" => ["مهندس برمجيات بخبرة تتجاوز ٤ سنوات في تحليل وتصميم وبناء حلول قابلة للتوسّع، أعمل كمستقل من الرياض وأخدم عملاء في كل السعودية.", "مهندس برمجيات بخبرة تتجاوز 4 سنوات في تحليل وتصميم وبناء حلول قابلة للتوسّع. أعمل مستقلاً من الرياض مع عملاء في كل السعودية."],
        "home_description_en" => ["Freelance software developer in Riyadh, Saudi Arabia. I build web & mobile apps, management systems, professional websites, and APIs for clients across Saudi Arabia. Turn your idea into a working product, first consultation free.", "Freelance software engineer in Riyadh for founders and business owners. I build your website, platform or app from idea to launch, with a written scope and public starting prices. First call free."],
        "services_description_en" => ["Custom systems, web & mobile apps, professional websites, integrations, and APIs, from a freelance software engineer based in Riyadh, Saudi Arabia.", "Six kinds of project with public starting prices and clear timelines: websites, stores and booking platforms, management systems, mobile apps, API integrations and AI automation. From a freelance software engineer in Riyadh."],
        "projects_description_en" => ["Delivered projects with a clear methodology: challenge, solution, and result. Systems, apps, and platforms built for clients across government, healthcare, education, and commerce.", "Real projects that are live today: a diwaniya booking platform, a finishing and renovation platform, and sites for independent professionals. Each with its story: challenge, solution, result."],
    ];

    public function up(): void
    {
        foreach (self::CHANGES as $key => [$old, $new]) {
            // The model decodes the stored JSON, so the comparison is on the
            // text itself however the database happened to escape it.
            Setting::query()
                ->where('group', 'seo')
                ->where('key', $key)
                ->get()
                ->filter(fn (Setting $setting) => $setting->value === $old)
                ->each(fn (Setting $setting) => $setting->update(['value' => $new]));
        }

        Cache::forget('site:settings');
    }

    public function down(): void
    {
        // Content-only change; nothing to roll back structurally.
    }
};
