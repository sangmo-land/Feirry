<?php

namespace App\Mail;

use App\Models\AssistanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Acknowledgement to the applicant (CDC Coup de Pouce §09): received, with
 * its reference — explicitly not an appointment.
 */
class AssistanceReceivedMail extends Mailable
{
    use Queueable;

    public function __construct(public AssistanceRequest $lead) {}

    public function envelope(): Envelope
    {
        $en = $this->lead->locale === 'en';

        return new Envelope(subject: $en
            ? "Coup de Pouce FEIRRY — request received ({$this->lead->reference})"
            : "Coup de Pouce FEIRRY — demande reçue ({$this->lead->reference})");
    }

    public function content(): Content
    {
        return new Content(text: 'mail.assistance-received');
    }
}
