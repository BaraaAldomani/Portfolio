<?php

namespace Database\Seeders;

use App\Models\ProcessStep;
use Illuminate\Database\Seeder;

class ProcessStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'title_ar' => 'نفهم',
                'title_en' => 'Understand',
                'blurb_ar' => 'نتحدّث عن هدفك وعملائك ونحدّد المطلوب بوضوح.',
                'blurb_en' => 'We talk about your goal and your customers, and define what is needed clearly.',
                'sort_order' => 1,
            ],
            [
                'title_ar' => 'نخطّط',
                'title_en' => 'Plan',
                'blurb_ar' => 'أقترح أوضح طريقة للتنفيذ مع جدولٍ ونطاقٍ متّفق عليه.',
                'blurb_en' => 'I propose the clearest way to build it, with an agreed scope and timeline.',
                'sort_order' => 2,
            ],
            [
                'title_ar' => 'نبني',
                'title_en' => 'Build',
                'blurb_ar' => 'تطوير بخطوات واضحة وتحديثات منتظمة تطمئنّك على التقدّم.',
                'blurb_en' => 'Development in clear steps with regular updates so you stay confident in progress.',
                'sort_order' => 3,
            ],
            [
                'title_ar' => 'نُطلق',
                'title_en' => 'Launch',
                'blurb_ar' => 'إطلاق سلس مع دعمٍ بعد التسليم لضمان الاستقرار.',
                'blurb_en' => 'A smooth launch with post-delivery support to ensure stability.',
                'sort_order' => 4,
            ],
        ];

        foreach ($steps as $step) {
            ProcessStep::updateOrCreate(['title_en' => $step['title_en']], $step);
        }
    }
}
