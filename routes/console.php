<?php

use App\Models\ContactMessage;
use App\Support\LeadNotifier;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('leads:test', function (LeadNotifier $notifier) {
    $sample = new ContactMessage([
        'name' => 'Test lead',
        'email' => config('portfolio.email'),
        'message' => 'This is a test of the contact-form alerts. If you can read it, alerts work.',
        'locale' => 'ar',
    ]);
    $sample->id = 0;

    $results = $notifier->notify($sample);

    if ($results === []) {
        $this->warn('No alert channel configured. Set TELEGRAM_BOT_TOKEN + TELEGRAM_CHAT_ID and/or LEAD_NOTIFY_EMAIL in .env.');

        return 1;
    }

    foreach ($results as $channel => $ok) {
        $ok ? $this->info("{$channel}: sent") : $this->error("{$channel}: failed (see storage/logs/laravel.log)");
    }

    return in_array(false, $results, true) ? 1 : 0;
})->purpose('Send a sample contact-form alert to check Telegram / email settings');
