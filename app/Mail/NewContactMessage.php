<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Support\LeadNotifier;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Owner alert for a new contact-form message. Reply-To is the visitor, so
 * hitting "Reply" in the inbox answers them directly.
 */
final class NewContactMessage extends Mailable
{
    public function __construct(public readonly ContactMessage $contact)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->contact->email, $this->contact->name)],
            subject: "رسالة جديدة من الموقع: {$this->contact->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.new-contact-message',
            with: ['dashboardUrl' => LeadNotifier::dashboardUrl($this->contact)],
        );
    }
}
