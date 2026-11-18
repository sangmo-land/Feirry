<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Internal notice that a request arrived. Carries the reference and a link to
 * the admin, not the request itself: personal data and documents stay on the
 * server (CDC §09).
 */
class NewLeadMail extends Mailable
{
    use Queueable;

    public function __construct(public string $kind, public string $reference, public string $adminUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[FEIRRY] Nouvelle demande {$this->kind} — {$this->reference}");
    }

    public function content(): Content
    {
        return new Content(text: 'mail.new-lead');
    }
}
