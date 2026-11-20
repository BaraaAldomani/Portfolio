<?php

namespace Tests\Feature;

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactLeadTest extends TestCase
{
    use RefreshDatabase;

    private const PAYLOAD = [
        'name' => 'Sara Client',
        'email' => 'sara@example.com',
        'message' => 'We need a booking system for our clinics in Riyadh.',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutDefer();
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
        Mail::fake();
    }

    public function test_a_message_is_stored_and_alerts_go_out_on_every_configured_channel(): void
    {
        config([
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.chat_id' => '12345',
            'portfolio.lead_notify_email' => 'owner@example.com',
        ]);

        $this->from('/ar/contact')->post('/ar/contact', self::PAYLOAD)
            ->assertRedirect('/ar/contact')
            ->assertSessionHas('contact_sent', true);

        $this->assertDatabaseHas('contact_messages', ['email' => 'sara@example.com', 'locale' => 'ar']);

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
            && $request['chat_id'] === '12345'
            && str_contains($request['text'], 'Sara Client')
            && str_contains($request['text'], 'booking system'));

        Mail::assertSent(NewContactMessage::class, fn (NewContactMessage $mail) => $mail->hasTo('owner@example.com')
            && $mail->hasReplyTo('sara@example.com'));
    }

    public function test_no_alerts_are_attempted_when_nothing_is_configured(): void
    {
        config([
            'services.telegram.bot_token' => null,
            'services.telegram.chat_id' => null,
            'portfolio.lead_notify_email' => null,
        ]);

        $this->post('/ar/contact', self::PAYLOAD)->assertSessionHas('contact_sent', true);

        Http::assertNothingSent();
        Mail::assertNothingSent();
    }

    public function test_a_failing_alert_channel_never_breaks_the_form(): void
    {
        config([
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.chat_id' => '12345',
        ]);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => false], 500)]);

        $this->post('/ar/contact', self::PAYLOAD)->assertSessionHas('contact_sent', true);

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_the_honeypot_rejects_bots_without_storing_or_alerting(): void
    {
        config(['portfolio.lead_notify_email' => 'owner@example.com']);

        $this->post('/ar/contact', [...self::PAYLOAD, 'website' => 'spam.example'])
            ->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }
}
