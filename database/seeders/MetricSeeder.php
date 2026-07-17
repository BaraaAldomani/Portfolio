<?php

namespace Database\Seeders;

use App\Models\Metric;
use Illuminate\Database\Seeder;

class MetricSeeder extends Seeder
{
    public function run(): void
    {
        $metrics = [
            // Home page (v3.metrics)
            ['context' => 'home', 'value' => 4, 'suffix' => '+', 'label_ar' => 'سنوات في بناء البرمجيات', 'label_en' => 'Years building software', 'sort_order' => 1],
            ['context' => 'home', 'value' => 10, 'suffix' => '+', 'label_ar' => 'أنظمة مُطلَقة', 'label_en' => 'Systems shipped', 'sort_order' => 2],
            ['context' => 'home', 'value' => 5, 'suffix' => '', 'label_ar' => 'قطاعات خُدمت', 'label_en' => 'Sectors served', 'sort_order' => 3],

            // Services page (services.stats.items)
            ['context' => 'services', 'value' => 4, 'suffix' => '+', 'label_ar' => 'سنوات خبرة', 'label_en' => 'Years of experience', 'sort_order' => 1],
            ['context' => 'services', 'value' => 10, 'suffix' => '+', 'label_ar' => 'مشروع منجز', 'label_en' => 'Projects delivered', 'sort_order' => 2],
            ['context' => 'services', 'value' => 5, 'suffix' => '', 'label_ar' => 'قطاعات خُدمت', 'label_en' => 'Sectors served', 'sort_order' => 3],
            ['context' => 'services', 'value' => 4, 'suffix' => '', 'label_ar' => 'مجالات خدمة أساسية', 'label_en' => 'Core service areas', 'sort_order' => 4],
        ];

        foreach ($metrics as $metric) {
            Metric::updateOrCreate(
                ['context' => $metric['context'], 'label_en' => $metric['label_en']],
                $metric,
            );
        }
    }
}
