<?php

namespace Database\Seeders;

use App\Models\FocusItem;
use Illuminate\Database\Seeder;

class FocusItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title_ar' => 'الواجهة الخلفية وواجهات API',
                'title_en' => 'Backend & APIs',
                'blurb_ar' => 'خدمات وواجهات API نظيفة وموثّقة تبني عليها الأنظمة والفِرق بثقة.',
                'blurb_en' => 'Clean, documented services and APIs that other systems and teams can build on with confidence.',
                'sort_order' => 1,
            ],
            [
                'title_ar' => 'أنظمة عالية الحِمل',
                'title_en' => 'High-load systems',
                'blurb_ar' => 'بنى معمارية تبقى سريعة ومستقرة تحت ضغطٍ كثيف ومتقطّع وعلى نطاق وطني.',
                'blurb_en' => 'Architectures that stay fast and stable under heavy, spiky traffic at national scale.',
                'sort_order' => 2,
            ],
            [
                'title_ar' => 'البيانات والتكاملات',
                'title_en' => 'Data & integrations',
                'blurb_ar' => 'نمذجة بيانات متينة، وقواعد بيانات، ومدفوعات، وتكاملات خارجية موثوقة.',
                'blurb_en' => 'Solid data modelling, databases, payments, and reliable third-party integrations.',
                'sort_order' => 3,
            ],
        ];

        foreach ($items as $item) {
            FocusItem::updateOrCreate(['title_en' => $item['title_en']], $item);
        }
    }
}
