<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

/**
 * Titles and descriptions brought within what a results page shows (a title
 * of at most 60 characters, a description of at most 160), and two titles
 * reworded to use the words the page itself uses.
 *
 * Like the earlier refresh, this touches the settings table on the server and
 * replaces a value only if it still holds the previous default: anything the
 * owner has edited in the dashboard is left alone.
 */
return new class extends Migration
{
    /** key => [previous default, new text] */
    private const CHANGES = [
        "home_description_ar" => ["مهندس برمجيات مستقل في الرياض لروّاد الأعمال وأصحاب المشاريع. أبني موقعك أو منصتك أو تطبيقك من الفكرة حتى الإطلاق، بنطاق مكتوب وسعر معلن. المكالمة الأولى مجانية.", "مهندس برمجيات مستقل في الرياض لروّاد الأعمال. أبني موقعك أو منصتك أو تطبيقك من الفكرة حتى الإطلاق، بنطاق مكتوب وسعر معلن. المكالمة الأولى مجانية."],
        "services_title_ar" => ["خدمات هندسة وتطوير البرمجيات | بَراء الدوماني، الرياض", "الخدمات والأسعار: مواقع ومنصات وتطبيقات | بَراء الدوماني"],
        "services_description_ar" => ["ستة أنواع من المشاريع بأسعار بداية معلنة ومدة واضحة: مواقع، متاجر ومنصات حجوزات، أنظمة إدارة، تطبيقات جوال، تكاملات API، وأتمتة بالذكاء الاصطناعي. من مهندس برمجيات مستقل في الرياض.", "ستة أنواع من المشاريع بأسعار بداية معلنة ومدة واضحة: مواقع، متاجر ومنصات حجوزات، أنظمة إدارة، تطبيقات جوال، تكاملات API، وأتمتة بالذكاء الاصطناعي."],
        "projects_title_ar" => ["أعمالي ودراسات الحالة | بَراء الدوماني", "الأعمال: منصات ومواقع بنيتُها وتعمل الآن | بَراء الدوماني"],
        "home_description_en" => ["Freelance software engineer in Riyadh for founders and business owners. I build your website, platform or app from idea to launch, with a written scope and public starting prices. First call free.", "Freelance software engineer in Riyadh. I build your website, platform or app from idea to launch, with a written scope and public prices. First call free."],
        "services_title_en" => ["Software Engineering & Development Services | Baraa Aldomani, Riyadh", "Software Services & Prices in Riyadh | Baraa Aldomani"],
        "services_description_en" => ["Six kinds of project with public starting prices and clear timelines: websites, stores and booking platforms, management systems, mobile apps, API integrations and AI automation. From a freelance software engineer in Riyadh.", "Public starting prices and clear timelines for six kinds of project: websites, booking platforms, management systems, mobile apps, integrations and AI."],
        "projects_title_en" => ["Projects & Case Studies | Baraa Aldomani", "Work: Platforms and Websites I Have Built | Baraa Aldomani"],
        "projects_description_en" => ["Real projects that are live today: a diwaniya booking platform, a finishing and renovation platform, and sites for independent professionals. Each with its story: challenge, solution, result.", "Real projects that are live today: a diwaniya booking platform, a renovation platform, and sites for independent professionals. Each with its story."],
        "about_description_en" => ["Software engineer with 4+ years analyzing, designing, and building scalable solutions, working as a freelancer from Riyadh and serving clients across Saudi Arabia.", "Software engineer with 4+ years analyzing, designing and building scalable solutions. Freelancing from Riyadh with clients across Saudi Arabia."],
    ];

    public function up(): void
    {
        foreach (self::CHANGES as $key => [$old, $new]) {
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
