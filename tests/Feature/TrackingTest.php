<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    private function setting(string $key, string $value): void
    {
        Setting::create(['group' => 'tracking', 'key' => $key, 'value' => $value]);
        app(SiteContent::class)->flush();
    }

    public function test_verification_meta_accepts_a_pasted_meta_tag_or_a_bare_code(): void
    {
        $this->setting('google_verification', '<meta name="google-site-verification" content="abc123XYZ" />');
        $this->setting('bing_verification', 'BING-CODE-42');

        $this->get('/ar')
            ->assertOk()
            ->assertSee('<meta name="google-site-verification" content="abc123XYZ">', false)
            ->assertSee('<meta name="msvalidate.01" content="BING-CODE-42">', false);
    }

    public function test_analytics_scripts_stay_off_outside_production(): void
    {
        $this->setting('umami_website_id', '11111111-2222-3333-4444-555555555555');

        $this->get('/ar')->assertOk()->assertDontSee('data-website-id', false);
    }

    public function test_analytics_scripts_load_in_production(): void
    {
        $this->app['env'] = 'production';
        $this->setting('umami_website_id', '11111111-2222-3333-4444-555555555555');
        $this->setting('ga4_id', 'G-TEST123');

        $this->get('/ar')
            ->assertOk()
            ->assertSee('data-website-id="11111111-2222-3333-4444-555555555555"', false)
            ->assertSee('googletagmanager.com/gtag/js?id=G-TEST123', false);
    }

    public function test_whatsapp_links_carry_the_prefilled_greeting(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('class="wa-fab"', false)
            ->assertSee('wa.me/966553785576?text=Hi%20Baraa', false);
    }

    public function test_the_analytics_dashboard_page_opens_for_admins(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/tracking-settings')
            ->assertOk()
            ->assertSee('Google Search Console');
    }
}
