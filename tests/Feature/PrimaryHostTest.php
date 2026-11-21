<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The site answers on one address (PRIMARY_URL). This is what moves it to a
 * new domain, so the old address must hand every page over, not just the home.
 */
class PrimaryHostTest extends TestCase
{
    use RefreshDatabase;

    public function test_without_a_primary_address_the_site_answers_where_it_is_asked(): void
    {
        config(['portfolio.primary_url' => null]);

        $this->get('http://anything.test/ar/about')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="http://anything.test/ar/about">', false);
    }

    public function test_any_other_host_is_sent_to_the_same_page_on_the_primary_address(): void
    {
        config(['portfolio.primary_url' => 'https://baraadom.com', 'portfolio.redirect_marker' => null]);

        // The older domain, with its path and query kept.
        $moved = $this->get('https://baraa.rakeez-llc.com/ar/about?utm_source=card')
            ->assertStatus(301)
            ->assertRedirect('https://baraadom.com/ar/about?utm_source=card');

        // No cache in between may keep it: a CDN that held the redirect of the
        // day before a move sent visitors round in a circle (21 Nov 2026).
        $this->assertStringContainsString('no-store', $moved->headers->get('Cache-Control'));

        // www and plain http of the new one.
        $this->get('http://www.baraadom.com/en')->assertStatus(301)->assertRedirect('https://baraadom.com/en');

        // Files search engines ask for by name move too.
        $this->get('https://baraa.rakeez-llc.com/sitemap.xml')->assertStatus(301)->assertRedirect('https://baraadom.com/sitemap.xml');
    }

    public function test_a_marker_can_make_the_target_new_to_a_cache_that_holds_an_old_redirect(): void
    {
        config(['portfolio.primary_url' => 'https://baraadom.com', 'portfolio.redirect_marker' => 'moved']);

        $this->get('https://baraa.rakeez-llc.com/ar')->assertStatus(301)->assertRedirect('https://baraadom.com/ar?moved=1');
        $this->get('https://baraa.rakeez-llc.com/ar/about?utm_source=card')
            ->assertRedirect('https://baraadom.com/ar/about?utm_source=card&moved=1');

        // On the primary address itself the marker changes nothing: same page, clean canonical.
        $this->get('https://baraadom.com/ar/about?moved=1')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://baraadom.com/ar/about">', false);
    }

    public function test_on_the_primary_address_every_link_the_site_writes_uses_it(): void
    {
        config(['portfolio.primary_url' => 'https://baraadom.com/']);

        $html = $this->get('https://baraadom.com/ar/about')->assertOk()->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="https://baraadom.com/ar/about">', $html);
        $this->assertStringContainsString('hreflang="en" href="https://baraadom.com/en/about"', $html);
        $this->assertStringContainsString('"@id":"https://baraadom.com#person"', $html);
        // The share picture is the one with this address drawn into it.
        $this->assertStringContainsString('content="https://baraadom.com/images/og-baraadom.png"', $html);
        $this->assertStringNotContainsString('rakeez-llc.com/ar', $html);

        $this->get('https://baraadom.com/en')->assertOk()->assertSee('https://baraadom.com/images/og-baraadom-en.png', false);
        $this->get('https://baraadom.com/sitemap.xml')->assertOk()->assertSee('<loc>https://baraadom.com/ar</loc>', false);
        $this->get('https://baraadom.com/robots.txt')->assertOk()->assertSee('Sitemap: https://baraadom.com/sitemap.xml', false);
    }

    public function test_a_host_without_its_own_share_picture_uses_the_default_one(): void
    {
        config(['portfolio.primary_url' => 'https://example.test']);

        $this->get('https://example.test/ar')->assertOk()->assertSee('https://example.test/images/og.png', false);
    }
}
