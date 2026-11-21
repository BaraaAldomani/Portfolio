<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Support\ServicePages;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProjectSeeder::class);
    }

    private function path(string $key, string $locale): string
    {
        return "/{$locale}/services/" . rawurlencode(ServicePages::slug($key, $locale));
    }

    /** The JSON-LD graph of a rendered page, as nodes keyed by nothing in particular. */
    private function graph(string $html): array
    {
        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        return json_decode($m[1], true, flags: JSON_THROW_ON_ERROR)['@graph'];
    }

    public function test_every_service_has_its_own_page_in_both_languages(): void
    {
        $this->assertCount(6, ServicePages::keys());

        foreach (ServicePages::keys() as $key) {
            foreach (['ar', 'en'] as $locale) {
                $content = ServicePages::content($key, $locale);

                $this->get($this->path($key, $locale))
                    ->assertOk()
                    ->assertSee('<title>' . e($content['seo_title']) . '</title>', false)
                    ->assertSee($content['faq'][0]['q'])
                    ->assertSee('data-estimator', false)
                    ->assertDontSee('noindex', false)
                    // Nothing left unfilled, and nothing missing a translation.
                    ->assertDontSee(':from', false)
                    ->assertDontSee(':time', false)
                    ->assertDontSee('service_pages.', false);
            }
        }
    }

    public function test_a_service_page_reads_its_price_from_the_price_guide(): void
    {
        config(['pricing.offers.app.from' => 4321]);

        $html = $this->get($this->path('app', 'ar'))->assertOk()->assertSee('4,321')->getContent();

        $service = collect($this->graph($html))->firstWhere('@type', 'Service');
        $this->assertSame(4321, $service['offers']['priceSpecification']['minPrice']);
        $this->assertSame('SAR', $service['offers']['priceCurrency']);
    }

    public function test_a_service_page_says_what_it_is_in_structured_data(): void
    {
        $html = $this->get($this->path('platform', 'ar'))->assertOk()->getContent();
        $graph = collect($this->graph($html));

        // Home > Services > this service.
        $trail = $graph->firstWhere('@type', 'BreadcrumbList')['itemListElement'];
        $this->assertCount(3, $trail);
        $this->assertSame(__('service_pages.items.platform.name', [], 'ar'), $trail[2]['name']);

        // Its own questions, exactly as the visitor reads them, and not the general ones.
        $content = ServicePages::content('platform', 'ar');
        $faq = $graph->firstWhere('@type', 'FAQPage');
        $this->assertSame(array_column($content['faq'], 'q'), array_column($faq['mainEntity'], 'name'));
        $this->assertSame(1, $graph->where('@type', 'FAQPage')->count());
        foreach ($content['faq'] as $qa) {
            $this->assertSame(2, substr_count($html, e($qa['q'])), 'each question is on the page once and in the JSON-LD once');
        }

        // The page is about the service, and the site-wide catalogue points here.
        $page = $graph->firstWhere('@type', 'WebPage');
        $this->assertStringEndsWith('#service', $page['mainEntity']['@id']);
        $offers = $graph->firstWhere('@type', 'ProfessionalService')['hasOfferCatalog']['itemListElement'];
        $this->assertContains(ServicePages::url('platform', 'ar'), array_column($offers, 'url'));
    }

    public function test_a_service_page_shows_real_work_of_its_kind(): void
    {
        $sajjah = Project::where('scene', 'sajjah')->first();

        $this->get($this->path('platform', 'ar'))
            ->assertOk()
            ->assertSee('class="win framed', false)
            ->assertSee($sajjah->name_ar)
            ->assertSee('projects/' . rawurlencode($sajjah->slug_ar), false);

        // No client project yet for automation: the page shows the method instead, never an empty section.
        $this->get($this->path('ai', 'ar'))
            ->assertOk()
            ->assertDontSee('class="win framed', false)
            ->assertSee(__('site.ai.points.0.title', [], 'ar'));

        // And a project page points back to the service it is an example of.
        $this->get('/ar/projects/' . rawurlencode($sajjah->slug_ar))
            ->assertOk()
            ->assertSee(ServicePages::url('platform', 'ar'), false);
    }

    public function test_a_slug_from_the_other_language_lands_on_the_right_page(): void
    {
        $this->get('/en/services/' . rawurlencode(ServicePages::slug('site', 'ar')))
            ->assertRedirect(ServicePages::url('site', 'en'))
            ->assertStatus(301);

        $this->get('/ar/services/no-such-service')->assertNotFound();
    }

    public function test_the_site_leads_to_every_service_page(): void
    {
        $home = $this->get('/ar')->assertOk();
        $index = $this->get('/ar/services')->assertOk();
        $sitemap = $this->get('/sitemap.xml')->assertOk();
        $llms = $this->get('/llms.txt')->assertOk();

        foreach (ServicePages::keys() as $key) {
            $ar = ServicePages::url($key, 'ar');
            $en = ServicePages::url($key, 'en');

            $home->assertSee($ar, false);
            $index->assertSee($ar, false);
            $sitemap->assertSee('<loc>' . $ar . '</loc>', false)->assertSee('<loc>' . $en . '</loc>', false);
            $llms->assertSee($en, false);
        }

        // Each language lists the other as its alternate.
        $this->get($this->path('system', 'ar'))
            ->assertSee('hreflang="en" href="' . ServicePages::url('system', 'en') . '"', false);
    }

    public function test_every_service_has_its_own_words(): void
    {
        foreach (['ar', 'en'] as $locale) {
            $pages = collect(ServicePages::keys())->map(fn (string $key): array => ServicePages::content($key, $locale));

            // Address, title, description and headings: none shared between two services.
            foreach (['slug', 'seo_title', 'seo_description', 'title', 'fit_title', 'gets_title', 'estimate_title', 'faq_title'] as $field) {
                $this->assertCount(6, $pages->pluck($field)->unique(), "{$field} repeats in {$locale}");
            }

            // A "not for you" note may only send the reader to another real service.
            $pages->each(fn (array $page) => $this->assertContains($page['not_fit']['to'] ?? 'site', ServicePages::keys()));
        }
    }

    public function test_the_proof_on_a_page_is_work_of_that_kind_and_says_what_it_is(): void
    {
        // Websites show the two personal sites and none of the platforms.
        $this->get($this->path('site', 'ar'))
            ->assertOk()
            ->assertSee('images/projects/rouaa', false)
            ->assertDontSee('images/projects/sajjah', false)
            ->assertSee(ServicePages::content('site', 'ar')['proof_lead']);

        // Work without a screenshot is listed as a row, under words that say it is earlier work.
        $this->get($this->path('api', 'ar'))
            ->assertOk()
            ->assertDontSee('class="win framed', false)
            ->assertSee(ServicePages::content('api', 'ar')['proof_lead'])
            ->assertSee(Project::where('slug_en', 'learny-language-learning-platform')->value('name_ar'));
    }

    public function test_the_page_says_who_does_the_work_and_when_prices_were_checked(): void
    {
        config(['pricing.reviewed' => '2026-03-09']);

        $this->get($this->path('system', 'en'))
            ->assertOk()
            ->assertSee('href="' . route('about', ['locale' => 'en']) . '"', false)
            ->assertSee('Prices last updated: March 2026')
            ->assertSee('"dateModified":"2026-03-09"', false);

        $this->get('/sitemap.xml')->assertOk()->assertSee('<lastmod>2026-03-09</lastmod>', false);
    }

    public function test_durations_are_written_as_each_language_says_them(): void
    {
        $this->assertSame('من أسبوع إلى أسبوعين', weeks_span(1, 2, 'ar'));
        $this->assertSame('من أسبوع إلى 3 أسابيع', weeks_span(1, 3, 'ar'));
        $this->assertSame('من 8 إلى 12 أسبوعاً', weeks_span(8, 12, 'ar'));
        $this->assertSame('1 to 2 weeks', weeks_span(1, 2, 'en'));

        // The calculator opens on the page's own service, with its duration in words.
        $this->get($this->path('site', 'ar'))->assertOk()->assertSee('data-est="weeks">من أسبوع إلى أسبوعين<', false);
    }
}
