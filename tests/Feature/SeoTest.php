<?php

namespace Tests\Feature;

use App\Models\Project;
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
                ->assertSee('hreflang="x-default"', false)
                ->assertSee('property="og:url"', false)
                ->assertSee('property="og:locale"', false)
                ->assertSee('name="twitter:card" content="summary_large_image"', false)
                ->assertSee('images/og.png', false);
        }
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
}
