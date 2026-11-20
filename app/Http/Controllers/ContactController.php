<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Support\LeadNotifier;
use App\Support\Seo\PageMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function Illuminate\Support\defer;

final class ContactController
{
    public function show(): View
    {
        return view('pages.contact', [
            'meta' => PageMeta::fromLang('contact'),
        ]);
    }

    public function store(ContactRequest $request, LeadNotifier $notifier): RedirectResponse
    {
        $message = ContactMessage::create([
            ...$request->safe()->only(['name', 'email', 'message']),
            'locale' => app()->getLocale(),
            'ip' => $request->ip(),
        ]);

        // Alert the owner after the response is sent, so a slow Telegram or
        // SMTP call never delays (or breaks) the visitor's confirmation.
        defer(fn () => $notifier->notify($message));

        return back()->with('contact_sent', true);
    }
}
