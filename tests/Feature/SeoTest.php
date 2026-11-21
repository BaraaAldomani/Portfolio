<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Support\ServicePages;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProjectSeeder::class);
    }

    public function test_every_page_carries_share_and_language_tags(): void
    {
        foreach (['/ar', '/en', '/ar/services', '/ar/projects', '/ar/about', '/ar/contact'] as $path) {
            $this->get($path)->assertOk()
                ->assertSee('property="og:url"', false)
                ->assertSee('property="og:locale"', false)
                ->assertSee('name="twitter:card" content="summary_large_image"', false)
                // The share picture carries a headline, so each language has its own.
                ->assertSee(str_starts_with($path, '/en') ? 'images/og-en.png' : 'images/og.png', false);
        }
    }

    public function test_both_languages_of_a_page_name_the_same_default(): void
    {
        // x-default is one address per pair (the Arabic page), in the page and in the sitemap.
        $default = 'hreflang="x-default" href="' . route('about', ['locale' => 'ar']) . '"';

        $this->get('/ar/about')->assertOk()->assertSee($default, false);
        $this->get('/en/about')->assertOk()->assertSee($default, false);
        $this->get('/sitemap.xml')->assertOk()->assertSee($default, false);
    }

    public function test_named_crawlers_are_kept_out_of_the_dashboard_too(): void
    {
        $robots = $this->get('/robots.txt')->assertOk()->getContent();

        // A crawler obeys only the group that names it: every group needs the rule.
        foreach (preg_split('/\n\s*\n/', trim($robots)) as $group) {
            if (str_contains($group, 'User-agent:')) {
                $this->assertStringContainsString('Disallow: /admin', $group);
            }
        }
        $this->assertStringContainsString('User-agent: GPTBot', $robots);
        $this->assertStringContainsString('User-agent: ClaudeBot', $robots);
    }

    public function test_a_missing_page_is_answered_in_the_visitors_language(): void
    {
        $this->get('/en/no-such-page')->assertNotFound()
            ->assertSee('<html lang="en" dir="ltr">', false)
            ->assertSee(__('site.pages.missing.title', [], 'en'))
            ->assertSee('noindex', false)
            ->assertSee('href="' . route('services', ['locale' => 'en']) . '"', false);

        $this->get('/ar/no-such-page')->assertNotFound()->assertSee(__('site.pages.missing.title', [], 'ar'));
    }

    public function test_a_project_asked_for_under_the_other_languages_slug_is_redirected(): void
    {
        $project = Project::first();

        $this->get('/en/projects/' . rawurlencode($project->slug_ar))
            ->assertStatus(301)
            ->assertRedirect(route('projects.show', ['locale' => 'en', 'project' => $project->slug_en]));

        $this->get('/ar/projects/no-such-project')->assertNotFound();
    }

    public function test_the_graph_points_each_page_at_what_it_is_about(): void
    {
        $graph = function (string $path): \Illuminate\Support\Collection {
            preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $this->get($path)->assertOk()->getContent(), $m);

            return collect(json_decode($m[1], true, flags: JSON_THROW_ON_ERROR)['@graph']);
        };

        $project = Project::whereNotNull('live_url')->first();
        $nodes = $graph('/ar/projects/' . rawurlencode($project->slug_ar));
        $work = $nodes->firstWhere('@type', 'CreativeWork');

        // The case study is about the work; the live site is an example of it, not "the same thing".
        $this->assertSame($work['@id'], $nodes->firstWhere('@type', 'WebPage')['mainEntity']['@id']);
        $this->assertSame($project->live_url, $work['workExample']['url']);
        $this->assertArrayNotHasKey('sameAs', $work);

        // The business is named as people know it, and the name without its vowel mark is given too.
        $home = $graph('/ar');
        $this->assertSame(__('common.brand', [], 'ar'), $home->firstWhere('@type', 'ProfessionalService')['name']);
        $this->assertContains('براء الدوماني', $home->firstWhere('@type', 'Person')['alternateName']);
        $this->assertStringNotContainsString('mailto:', $home->firstWhere('@type', 'Person')['email']);

        // Each offer in the catalogue names the service's own page node.
        $offer = $home->firstWhere('@type', 'ProfessionalService')['hasOfferCatalog']['itemListElement'][0];
        $this->assertSame(\App\Support\ServicePages::url('site', 'ar') . '#service', $offer['itemOffered']['@id']);
    }

    public function test_structured_data_says_what_each_page_is(): void
    {
        // Prices come from the price guide, on the pages that show them.
        config(['pricing.offers.site.from' => 4321]);
        $this->get('/ar/services')->assertOk()->assertSee('OfferCatalog', false)->assertSee('4321', false)->assertSee('FAQPage', false);

        // Questions are marked up only where they are on the page.
        $this->get('/ar/contact')->assertOk()->assertSee('ContactPage', false)->assertDontSee('FAQPage', false);

        $this->get('/ar/projects')->assertOk()->assertSee('BreadcrumbList', false)->assertSee('ItemList', false);

        $project = Project::where('scene', 'sajjah')->first();
        $this->get('/ar/projects/' . rawurlencode($project->slug_ar))->assertOk()->assertSee('CreativeWork', false);
    }

    public function test_the_empty_blog_is_kept_out_of_search(): void
    {
        $this->get('/ar/blog')->assertOk()->assertSee('noindex, follow', false);
        $this->get('/ar')->assertOk()->assertDontSee('noindex', false);

        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/blog', false)->assertSee('<lastmod>', false);
    }

    public function test_llms_txt_is_written_from_the_live_content(): void
    {
        config(['pricing.offers.site.from' => 4321]);
        $project = Project::where('scene', 'sajjah')->first();

        $this->get('/llms.txt')->assertOk()
            ->assertSee('4,321', false)
            ->assertSee($project->name_en, false)
            ->assertDontSee('Tawazoun', false);
    }

    public function test_llms_txt_is_an_index_of_markdown_links_with_a_full_file_beside_it(): void
    {
        $index = $this->get('/llms.txt')->assertOk()->getContent();

        // The llmstxt.org shape: one title first, a summary, then lists of links.
        $this->assertStringStartsWith('# ', $index);
        $this->assertSame(1, preg_match_all('/^# /m', $index));
        $this->assertStringContainsString("\n> ", $index);
        $this->assertStringContainsString('](' . ServicePages::url('site', 'en') . ')', $index);
        $this->assertStringContainsString('](' . route('llms.full') . ')', $index);

        $full = $this->get('/llms-full.txt')->assertOk()->getContent();
        $this->assertStringContainsString(ServicePages::content('app', 'en')['faq'][0]['q'], $full);
        $this->assertStringContainsString(__('site.faq.items.2.q', [], 'en'), $full);

        // Plain text: nothing escaped for HTML, no template left unfilled.
        foreach ([$index, $full] as $text) {
            $this->assertDoesNotMatchRegularExpression('/&(amp|quot|#0?39);|:[a-z]+_from\b|\{\{|\{!!/', $text);
        }
    }

    public function test_titles_and_descriptions_fit_a_results_page(): void
    {
        $paths = [];
        foreach (['ar', 'en'] as $locale) {
            foreach (['', '/services', '/projects', '/about', '/contact'] as $page) {
                $paths[] = "/{$locale}{$page}";
            }
            foreach (ServicePages::keys() as $key) {
                $paths[] = "/{$locale}/services/" . rawurlencode(ServicePages::slug($key, $locale));
            }
            foreach (Project::all() as $project) {
                $paths[] = "/{$locale}/projects/" . rawurlencode($project->{"slug_{$locale}"});
            }
        }

        foreach ($paths as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            preg_match('#<title>(.*?)</title>#s', $html, $title);
            preg_match('#<meta name="description" content="([^"]*)"#', $html, $description);

            $this->assertLessThanOrEqual(60, mb_strlen(html_entity_decode($title[1])), "title of {$path}");
            $this->assertLessThanOrEqual(160, mb_strlen(html_entity_decode($description[1])), "description of {$path}");
            $this->assertGreaterThanOrEqual(50, mb_strlen(html_entity_decode($description[1])), "description of {$path}");
        }
    }

    public function test_the_first_picture_of_a_page_is_not_lazy(): void
    {
        $project = Project::where('scene', 'sajjah')->first();

        foreach (['/ar/projects', '/ar/projects/' . rawurlencode($project->slug_ar)] as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            preg_match('#<img[^>]*>#', strstr($html, '<main'), $first);

            $this->assertStringContainsString('fetchpriority="high"', $first[0], $path);
            $this->assertStringNotContainsString('loading="lazy"', $first[0], $path);
        }
    }
}
