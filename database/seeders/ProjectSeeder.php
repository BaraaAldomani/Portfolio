<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->projects() as $project) {
            Project::updateOrCreate(['slug_en' => $project['slug_en']], $project);
        }
    }

    /**
     * Real engagements from the CV, written as client-facing case studies
     * (challenge → solution → result) rather than tech specs.
     *
     * @return array<int, array<string, mixed>>
     */
    private function projects(): array
    {
        return [
            [
                'slug_ar' => 'منصة-توازن-لتوزيع-الأراضي',
                'slug_en' => 'tawazoun-land-eligibility-platform',
                'image_path' => 'images/projects/tawazoun-land-eligibility-platform.svg',
                'title_ar' => 'توازن — منصة استحقاق الأراضي',
                'title_en' => 'Tawazoun — Land Eligibility Platform',
                'sector_ar' => 'قطاع حكومي · إسكان',
                'sector_en' => 'Government · Housing',
                'summary_ar' => 'منصة وطنية تُقيّم استحقاق المتقدّمين لتوزيع الأراضي لحظياً وبعدالة، وتصمد أمام ضغط مئات الآلاف من المستخدمين.',
                'summary_en' => 'A national platform that scores applicants for land distribution in real time and fairly — while staying stable under hundreds of thousands of users.',
                'problem_ar' => 'احتاجت جهة إسكان حكومية إلى ترتيب أعداد ضخمة من المتقدّمين وفق نظام نقاط معقّد يجمع معايير اجتماعية ومالية، مع تحمّل موجات ضغط هائلة تُسقط الأنظمة التقليدية وتُفقد الناس ثقتها بالعدالة.',
                'problem_en' => 'A government housing entity needed to rank huge volumes of applicants through a complex points system spanning social and financial criteria — while absorbing traffic spikes that crash ordinary systems and erode public trust in fairness.',
                'solution_ar' => 'صمّمتُ محرّك التقييم ليعمل تحت تزامنٍ عالٍ: احتساب لحظي للنقاط، ومعالجة غير متزامنة تستوعب موجات الذروة دون تعطّل، وبنية تُبقي زمن الاستجابة قصيراً مهما زاد الحِمل.',
                'solution_en' => 'I engineered the evaluation engine for high concurrency: real-time scoring, asynchronous processing that absorbs peak waves without stalling, and an architecture that keeps response times short no matter the load.',
                'result_ar' => 'تعالج المنصة التقييمات لحظياً وعلى نطاق وطني، وتبقى سريعة ومستقرّة في أوقات الذروة — بما يدعم قراراً عادلاً وموثوقاً لتوزيع الأراضي.',
                'result_en' => 'The platform evaluates applicants in real time at national scale and stays fast and stable at peak — supporting fair, trustworthy land-distribution decisions.',
                'highlights_ar' => ['تزامن عالٍ', 'تقييم لحظي', 'نطاق وطني'],
                'highlights_en' => ['High concurrency', 'Real-time scoring', 'National scale'],
                'featured' => true,
                'sort_order' => 1,
            ],
            [
                'slug_ar' => 'عيادتي-نظام-حجوزات-طبية',
                'slug_en' => 'eyadti-medical-booking-system',
                'image_path' => 'images/projects/eyadti-medical-booking-system.svg',
                'title_ar' => 'عيادتي — نظام مواعيد طبية',
                'title_en' => 'Eyadti — Medical Appointments System',
                'sector_ar' => 'قطاع صحي',
                'sector_en' => 'Healthcare',
                'summary_ar' => 'نظام ويب وتطبيق جوال يربط الأطباء بالمرضى ويُنظّم المواعيد من الحجز حتى التذكير.',
                'summary_en' => 'A web + mobile system connecting doctors and patients and organizing appointments from booking to reminder.',
                'problem_ar' => 'كانت العيادات تُدير مواعيدها يدوياً، فتتكرّر التعارضات ويغيب المرضى عن مواعيدهم، ويضيع وقت طاقم الاستقبال في التنسيق الهاتفي.',
                'problem_en' => 'Clinics ran appointments manually, leading to double-bookings, missed visits, and reception staff losing hours to phone coordination.',
                'solution_ar' => 'بنيتُ نظاماً متكاملاً بواجهة ويب وتطبيق جوال: حجز ذاتي للمرضى، تقويم موحّد للأطباء، وتذكيرات آلية — بتصميم نظيف قابل للتوسّع.',
                'solution_en' => 'I built an end-to-end system with a web interface and a mobile app: patient self-booking, a unified physician calendar, and automated reminders — on a clean, extensible design.',
                'result_ar' => 'صار بإمكان المرضى الحجز في أي وقت، وتراجعت التعارضات وحالات التغيّب، وتحرّر وقت الطاقم لخدمة المراجعين.',
                'result_en' => 'Patients can book anytime, conflicts and no-shows dropped, and staff time was freed to actually serve patients.',
                'highlights_ar' => ['ويب + جوال', 'حجز ذاتي', 'تذكيرات آلية'],
                'highlights_en' => ['Web + mobile', 'Self-booking', 'Automated reminders'],
                'featured' => true,
                'sort_order' => 2,
            ],
            [
                'slug_ar' => 'منصة-برنتلي-للطباعة-حسب-الطلب',
                'slug_en' => 'printly-print-on-demand-marketplace',
                'image_path' => 'images/projects/printly-print-on-demand-marketplace.svg',
                'title_ar' => 'برنتلي — سوق طباعة حسب الطلب',
                'title_en' => 'Printly — Print-on-Demand Marketplace',
                'sector_ar' => 'تجارة إلكترونية',
                'sector_en' => 'E-commerce',
                'summary_ar' => 'سوق إلكتروني يجمع المصمّمين والعملاء لطباعة تصاميم على منتجات متنوّعة بنموذج تقاسم أرباح.',
                'summary_en' => 'An online marketplace that brings designers and customers together to print designs on products, with a profit-sharing model.',
                'problem_ar' => 'أراد المشروع جذب عدد كبير من المصمّمين بنموذج عمولة على كل تصميم يُباع، وهو ما يتطلّب منصة تربط التصميم والطلب والطباعة والدفع في تجربة واحدة سلسة.',
                'problem_en' => 'The venture aimed to attract many designers on a per-sale commission model — which demands a platform tying design, ordering, printing, and payments into one seamless flow.',
                'solution_ar' => 'قُدتُ المشروع تحليلاً وتطويراً: متجر للتصاميم، نظام طلبات وطباعة على منتجات متعدّدة، واحتساب عمولات المصمّمين تلقائياً — مع بنية نظيفة قابلة للنمو.',
                'solution_en' => 'I led the project from analysis to build: a design storefront, an ordering-and-printing system across multiple products, and automatic designer-commission accounting — on a clean, growth-ready architecture.',
                'result_ar' => 'تجربة شراء كاملة من اختيار التصميم حتى الدفع، ونموذج تقاسم أرباح يحفّز المصمّمين على الانضمام والاستمرار.',
                'result_en' => 'A complete purchase journey from picking a design to checkout, and a profit-sharing model that motivates designers to join and stay.',
                'highlights_ar' => ['سوق متعدّد البائعين', 'تقاسم أرباح', 'تجربة شراء كاملة'],
                'highlights_en' => ['Multi-vendor marketplace', 'Profit sharing', 'Full checkout journey'],
                'featured' => true,
                'sort_order' => 3,
            ],
            [
                'slug_ar' => 'ليرني-منصة-تعلّم-اللغات',
                'slug_en' => 'learny-language-learning-platform',
                'image_path' => 'images/projects/learny-language-learning-platform.svg',
                'title_ar' => 'ليرني — منصة تعلّم اللغات',
                'title_en' => 'Learny — Language Learning Platform',
                'sector_ar' => 'تعليم',
                'sector_en' => 'Education',
                'summary_ar' => 'منصة تربط المعلّمين الخصوصيين بالطلاب لتسهيل تعلّم اللغات، مع جلسات مباشرة داخل المنصة.',
                'summary_en' => 'A platform connecting private teachers with students to make language learning easy, with live sessions built in.',
                'problem_ar' => 'كان الوصول إلى معلّم لغة مناسب وتنظيم الجلسات والتواصل المباشر موزّعاً على أدوات متفرّقة، ما يُربك الطلاب والمعلّمين معاً.',
                'problem_en' => 'Finding the right language teacher, scheduling sessions, and meeting live were scattered across separate tools — confusing for students and teachers alike.',
                'solution_ar' => 'تولّيتُ قيادة المشروع وتحليل المتطلبات وتطوير الواجهة الخلفية: مطابقة المعلّم بالطالب، جدولة الجلسات، ومكالمات مباشرة مدمجة داخل المنصة.',
                'solution_en' => 'I led the project, shaped the requirements, and built the backend: teacher–student matching, session scheduling, and live calls integrated directly into the platform.',
                'result_ar' => 'مكان واحد يجمع رحلة التعلّم كاملة — من اختيار المعلّم حتى الجلسة المباشرة — لتجربة أبسط للطرفين.',
                'result_en' => 'One place for the whole learning journey — from choosing a teacher to the live session — a simpler experience for both sides.',
                'highlights_ar' => ['مطابقة معلّم وطالب', 'جلسات مباشرة', 'تجربة موحّدة'],
                'highlights_en' => ['Teacher matching', 'Live sessions', 'Unified experience'],
                'featured' => false,
                'sort_order' => 4,
            ],
        ];
    }
}
