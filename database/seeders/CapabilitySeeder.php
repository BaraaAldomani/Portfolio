<?php

namespace Database\Seeders;

use App\Models\Capability;
use Illuminate\Database\Seeder;

class CapabilitySeeder extends Seeder
{
    public function run(): void
    {
        $capabilities = [
            ['label_ar' => 'تطبيقات ويب متكاملة', 'label_en' => 'End-to-end web applications', 'sort_order' => 1],
            ['label_ar' => 'تطبيقات جوال', 'label_en' => 'Mobile applications', 'sort_order' => 2],
            ['label_ar' => 'أنظمة إدارة مخصّصة', 'label_en' => 'Custom management systems', 'sort_order' => 3],
            ['label_ar' => 'واجهات API وتكاملات', 'label_en' => 'APIs & integrations', 'sort_order' => 4],
            ['label_ar' => 'تحليل الأنظمة وتصميم البنية', 'label_en' => 'System analysis & architecture', 'sort_order' => 5],
            ['label_ar' => 'تحسين الأداء والقابلية للتوسّع', 'label_en' => 'Performance & scalability', 'sort_order' => 6],
        ];

        foreach ($capabilities as $capability) {
            Capability::updateOrCreate(['label_en' => $capability['label_en']], $capability);
        }
    }
}
