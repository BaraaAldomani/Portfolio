<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SitePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProjectSeeder::class);
    }

    public static function pages(): array
    {
        return [
            'home ar' => ['/ar'], 'home en' => ['/en'],
            'services ar' => ['/ar/services'], 'services en' => ['/en/services'],
            'work ar' => ['/ar/projects'], 'work en' => ['/en/projects'],
            'about ar' => ['/ar/about'], 'about en' => ['/en/about'],
            'contact ar' => ['/ar/contact'], 'contact en' => ['/en/contact'],
            'blog ar' => ['/ar/blog'],
        ];
    }

    #[DataProvider('pages')]
    public function test_every_public_page_renders(string $path): void
    {
        $this->get($path)->assertOk()->assertSee('class="grain"', false);
    }

    public function test_the_home_page_states_the_offer_with_prices_and_real_work(): void
    {
        $this->get('/ar')
            ->assertOk()
            ->assertSee(__('site.b.hero.l3', [], 'ar'))
            ->assertSee(number_format(config('pricing.offers.site.from')))
            ->assertSee('data-board=', false)
            ->assertSee('images/projects/sajjah.webp', false);
    }

    public function test_each_project_with_a_scene_is_a_stop_on_the_board(): void
    {
        $response = $this->get('/ar')->assertOk();

        foreach (['sajjah', 'shattib', 'rouaa', 'nour'] as $station) {
            $response->assertSee('data-station="' . $station . '"', false);
        }

        // Each stop has its card, and the board gets what it draws from the page.
        $response->assertSee('data-card', false)
            ->assertSee('shattib-plan.webp', false)     // Shattib: blueprint that gets built
            ->assertSee('data-retheme', false)          // Rouaa: re-tint
            ->assertSee(__('site.b.ideas.0.say', [], 'ar'))   // the hero loop, as real text
            ->assertSee(__('site.b.questions.0', [], 'ar'));

        // Projects without a scene are listed as earlier work, not dropped.
        $response->assertSee('برنتلي');
    }

    public function test_a_project_page_shows_its_window_and_work_without_a_screenshot_shows_none(): void
    {
        $sajjah = Project::where('scene', 'sajjah')->first();
        $this->get('/ar/projects/' . rawurlencode($sajjah->slug_ar))
            ->assertOk()
            ->assertSee('class="win framed', false)
            ->assertSee('images/projects/sajjah.webp', false);

        // Work without a screenshot is shown as typography, never a broken image.
        $earlier = Project::whereNull('image_path')->first();
        $this->get('/ar/projects/' . rawurlencode($earlier->slug_ar))
            ->assertOk()
            ->assertSee('typo-panel', false)
            ->assertDontSee('class="win framed', false);
    }

    public function test_the_home_page_is_the_board_in_both_languages(): void
    {
        foreach (['ar', 'en'] as $locale) {
            $this->get("/{$locale}")
                ->assertOk()
                ->assertSee('data-board=', false)
                ->assertSee(__('site.b.hero.l1', [], $locale))
                ->assertDontSee('noindex', false);
        }

        // The earlier 3D take is gone.
        $this->get('/ar/3d')->assertNotFound();
    }

    public function test_every_page_speaks_the_same_visual_language(): void
    {
        // Page heads end in the trace; services are class cards; work is windows.
        $this->get('/ar/services')->assertOk()->assertSee('class="trace"', false)->assertSee('class="uml framed', false);
        $this->get('/ar/projects')->assertOk()->assertSee('class="trace"', false)->assertSee('class="win framed', false);
        $this->get('/ar/about')->assertOk()->assertSee('class="trace"', false);
        $this->get('/ar/contact')->assertOk()->assertSee('class="trace"', false);
    }

    public function test_the_government_project_is_not_shown_anywhere(): void
    {
        $this->assertNull(Project::where('kind', 'gov')->first());

        foreach (['/ar', '/ar/projects', '/en', '/en/projects'] as $path) {
            $this->get($path)->assertOk()->assertDontSee('توازن')->assertDontSee('Tawazoun');
        }
    }

    public function test_every_project_has_a_case_study_page_in_both_languages(): void
    {
        foreach (Project::all() as $project) {
            $this->get('/ar/projects/' . rawurlencode($project->slug_ar))->assertOk()->assertSee($project->name_ar);
            $this->get('/en/projects/' . $project->slug_en)->assertOk()->assertSee($project->name_en);
        }
    }

    public function test_the_calculator_reads_its_prices_from_the_price_guide(): void
    {
        config(['pricing.offers.site.from' => 4321]);

        $this->get('/ar/services')
            ->assertOk()
            ->assertSee('data-estimator', false)
            ->assertSee('4,321')
            ->assertSee('"from":4321', false);
    }

    public function test_the_faq_on_the_page_matches_the_faq_structured_data(): void
    {
        $first = __('site.faq.items.0.q', [], 'ar');

        $html = $this->get('/ar')->assertOk()->getContent();

        $this->assertStringContainsString('FAQPage', $html);
        $this->assertSame(2, substr_count($html, $first), 'the question appears once on the page and once in the JSON-LD');
    }

    public function test_the_dashboard_pages_still_open(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        foreach (['', '/projects', '/experiences', '/capabilities', '/contact-messages', '/image-settings', '/about-settings', '/contact-settings', '/seo-settings', '/theme-settings', '/tracking-settings'] as $path) {
            $this->actingAs($admin)->get('/admin' . $path)->assertOk();
        }
    }

    public function test_the_dashboard_project_form_opens_with_the_showcase_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $project = Project::first();

        $this->actingAs($admin)->get("/admin/projects/{$project->id}/edit")
            ->assertOk()
            ->assertSee('Live site URL')
            ->assertSee('Phone screenshot');
    }
}
