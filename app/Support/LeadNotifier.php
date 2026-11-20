<?php

namespace App\Support;

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Alerts the owner the moment a contact-form message arrives, so no lead sits
 * unread in the dashboard. Each channel (Telegram, email) is optional and
 * independent: a misconfigured or failing channel is reported and skipped,
 * never surfaced to the visitor.
 */
final class LeadNotifier
{
    /**
     * @return array<string, bool> channel => delivered
     */
    public function notify(ContactMessage $message): array
    {
        $results = [];

        if ($this->telegramConfigured()) {
            $results['telegram'] = $this->attempt(fn () => $this->sendTelegram($message));
        }

        if ($email = config('portfolio.lead_notify_email')) {
            $results['email'] = $this->attempt(fn () => Mail::to($email)->send(new NewContactMessage($message)));
        }

        return $results;
    }

    public function telegramConfigured(): bool
    {
        return filled(config('services.telegram.bot_token')) && filled(config('services.telegram.chat_id'));
    }

    private function sendTelegram(ContactMessage $message): void
    {
        $token = config('services.telegram.bot_token');

        Http::timeout(8)
            ->asJson()
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => config('services.telegram.chat_id'),
                'text' => $this->telegramText($message),
                'disable_web_page_preview' => true,
            ])
            ->throw();
    }

    private function telegramText(ContactMessage $message): string
    {
        // Plain text (no parse_mode) so visitor input never needs escaping.
        return implode("\n", [
            '📩 رسالة جديدة من الموقع',
            '',
            "الاسم: {$message->name}",
            "البريد: {$message->email}",
            'اللغة: ' . strtoupper((string) $message->locale),
            '',
            Str::limit($message->message, 3500),
            '',
            self::dashboardUrl($message),
        ]);
    }

    public static function dashboardUrl(ContactMessage $message): string
    {
        return url("/admin/contact-messages/{$message->getKey()}");
    }

    /**
     * @param  callable(): mixed  $send
     */
    private function attempt(callable $send): bool
    {
        try {
            $send();

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
