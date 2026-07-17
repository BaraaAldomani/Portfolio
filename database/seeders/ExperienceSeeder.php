<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $experiences = [
            [
                'role_ar' => 'مؤسِّس',
                'role_en' => 'Founder',
                'org_ar' => 'ركيز',
                'org_en' => 'Rakeez',
                'period_ar' => '2024 - الآن',
                'period_en' => '2024 - Now',
                'url' => 'https://rakeez-llc.com/',
                'blurb_ar' => 'أسّستُ وأقود استوديو برمجيات يقدّم الهندسة والمنتجات لعملاء في قطاعات متعدّدة.',
                'blurb_en' => 'Founded and lead a software studio that delivers engineering and products for clients across sectors.',
                'sort_order' => 1,
            ],
            [
                'role_ar' => 'مهندس برمجيات، أنظمة عالية الحِمل',
                'role_en' => 'Software Engineer, high-load systems',
                'org_ar' => 'الشركة الوطنية للإسكان (NHC)',
                'org_en' => 'National Housing Company (NHC)',
                'period_ar' => '2024 - الآن',
                'period_en' => '2024 - Now',
                'url' => 'https://nhci.sa/',
                'blurb_ar' => 'بناء وصيانة أنظمة وطنية لقطاع العقار داخل جهة حكومية، مع التركيز على الأداء والموثوقية على نطاق واسع.',
                'blurb_en' => 'Building and maintaining national systems for the real-estate sector within a government entity, focused on performance and reliability at scale.',
                'sort_order' => 2,
            ],
            [
                'role_ar' => 'قائد فريق الواجهة الخلفية',
                'role_en' => 'Backend Team Leader',
                'org_ar' => 'AldrTech',
                'org_en' => 'AldrTech',
                'period_ar' => '2023 - 2024',
                'period_en' => '2023 - 2024',
                'url' => 'https://sy.linkedin.com/company/aldrtech',
                'blurb_ar' => 'قيادة مشاريع رئيسية وتصميم بنية موحّدة، وإرشاد أعضاء الفريق مع الاستمرار في التطوير العملي.',
                'blurb_en' => 'Led key projects and designed a unified architecture, and mentored team members while continuing hands-on development.',
                'sort_order' => 3,
            ],
            [
                'role_ar' => 'محلّل نظم',
                'role_en' => 'System Analyst',
                'org_ar' => 'Smart Soft Services',
                'org_en' => 'Smart Soft Services',
                'period_ar' => '2022 - 2023',
                'period_en' => '2022 - 2023',
                'url' => 'https://www.smartsoft-sy.net/',
                'blurb_ar' => 'تحليل العمليات والمتطلبات لمشاريع واقعية، وتصميم قواعد البيانات ونماذج العمليات.',
                'blurb_en' => 'Analyzed processes and requirements for real-world projects, and designed databases and business-process models.',
                'sort_order' => 4,
            ],
        ];

        foreach ($experiences as $experience) {
            Experience::updateOrCreate(
                ['org_en' => $experience['org_en'], 'role_en' => $experience['role_en']],
                $experience,
            );
        }
    }
}
