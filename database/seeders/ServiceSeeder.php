<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'key' => 'systems',
                'icon_key' => 'systems',
                'title_ar' => 'أنظمة إدارة مخصّصة',
                'title_en' => 'Custom Management Systems',
                'description_ar' => 'أنظمة داخلية تُبنى حول طريقة عمل مؤسستك الفعلية بدل أن تجبرك على تغيير طريقة عملك.',
                'description_en' => 'Internal systems built around how your organization actually works, instead of forcing you to change how you operate.',
                'points_ar' => ['إدارة العمليات والصلاحيات', 'تقارير ولوحات تحكم', 'أتمتة المهام المتكرّرة'],
                'points_en' => ['Operations & permissions', 'Reports & dashboards', 'Automating repetitive tasks'],
                'tag_ar' => 'أدوات داخلية',
                'tag_en' => 'Internal tools',
                'image_alt_ar' => 'رسم توضيحي للوحة تحكّم نظام إدارة مخصّص',
                'image_alt_en' => 'Illustration of a custom management system dashboard',
                'sort_order' => 1,
            ],
            [
                'key' => 'web_apps',
                'icon_key' => 'web_apps',
                'title_ar' => 'تطبيقات ويب وجوال',
                'title_en' => 'Web & Mobile Apps',
                'description_ar' => 'منصات تصل إلى عملائك أينما كانوا، بأداءٍ سريع وتجربةٍ سلسة وموثوقة.',
                'description_en' => 'Platforms that reach your customers wherever they are, with fast performance and a smooth, reliable experience.',
                'points_ar' => ['واجهات سريعة وآمنة', 'تطبيقات جوال', 'قابلية للتوسّع مع نموّك'],
                'points_en' => ['Fast, secure interfaces', 'Mobile applications', 'Scales as you grow'],
                'tag_ar' => 'ويب وجوال',
                'tag_en' => 'Web & mobile',
                'image_alt_ar' => 'رسم توضيحي لتطبيق ويب وجوال',
                'image_alt_en' => 'Illustration of a web and mobile application',
                'sort_order' => 2,
            ],
            [
                'key' => 'websites',
                'icon_key' => 'websites',
                'title_ar' => 'مواقع احترافية',
                'title_en' => 'Professional Websites',
                'description_ar' => 'مواقع تعريفية ثنائية اللغة تُمثّل علامتك جيداً وتظهر حيث يبحث عملاؤك.',
                'description_en' => 'Bilingual marketing sites that represent your brand well and show up where your customers are searching.',
                'points_ar' => ['تصميم متجاوب', 'تهيئة لمحركات البحث (SEO)', 'سرعة تحميل عالية'],
                'points_en' => ['Responsive design', 'Search engine optimization (SEO)', 'High loading speed'],
                'tag_ar' => 'مواقع تسويقية',
                'tag_en' => 'Marketing sites',
                'image_alt_ar' => 'رسم توضيحي لموقع احترافي ثنائي اللغة',
                'image_alt_en' => 'Illustration of a professional bilingual website',
                'sort_order' => 3,
            ],
            [
                'key' => 'apis',
                'icon_key' => 'apis',
                'title_ar' => 'تكاملات وواجهات API',
                'title_en' => 'Integrations & APIs',
                'description_ar' => 'ربط أنظمتك ببعضها ومع خدمات خارجية بشكلٍ موثوق وآمن وبتوثيقٍ واضح.',
                'description_en' => 'Connecting your systems to each other and to external services reliably, securely, and with clear documentation.',
                'points_ar' => ['تكامل بوابات الدفع', 'واجهات API موثّقة', 'ربط الأنظمة الخارجية'],
                'points_en' => ['Payment gateway integration', 'Documented APIs', 'Third-party system connections'],
                'tag_ar' => 'تكاملات',
                'tag_en' => 'Integrations',
                'image_alt_ar' => 'رسم توضيحي للتكاملات وواجهات API تربط الأنظمة',
                'image_alt_en' => 'Illustration of integrations and APIs connecting systems',
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['key' => $service['key']], $service);
        }
    }
}
