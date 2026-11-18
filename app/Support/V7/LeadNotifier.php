<?php

namespace App\Support\V7;

use App\Mail\NewLeadMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadNotifier
{
    public static function send(string $kind, string $reference, string $adminUrl): void
    {
        $to = config('site.leads.notify');

        if (blank($to)) {
            return;
        }

        try {
            Mail::to($to)->send(new NewLeadMail($kind, $reference, $adminUrl));
        } catch (\Throwable $e) {
            // The request is already saved: a mail failure must not lose it.
            // Only the reference is logged, never the content of the request.
            Log::warning("Lead notification failed for {$reference}: ".$e->getMessage());
        }
    }
}
