@php
    $booked = $lead->slot_key && $lead->scheduled_at;
    $en = $lead->locale === 'en';
    $when = $booked ? $lead->scheduled_at->locale($en ? 'en' : 'fr')->isoFormat($en ? 'dddd D MMMM YYYY [at] HH:mm' : 'dddd D MMMM YYYY [à] HH[ h ]mm') : null;
    $where = ['office' => [$en ? 'at the FEIRRY office, Carrefour BP Cité, Douala' : 'au bureau FEIRRY, Carrefour BP Cité, Douala'], 'video' => [$en ? 'by video call (the link will be sent to you)' : 'en visioconférence (le lien vous sera envoyé)'], 'site' => [$en ? 'on your site' : 'sur votre chantier']][$lead->scheduled_mode][0] ?? '';
@endphp
@if ($en)
Hello {{ $lead->name }},

Thank you, your Coup de Pouce FEIRRY request has been received.
Reference: {{ $lead->reference }}

@if ($booked)
Your {{ $lead->path === 'visit' ? 'site visit' : 'consultation' }} is booked for {{ $when }} (Douala time), {{ $where }}.
If you cannot attend, or if FEIRRY has to move the appointment, we will arrange it with you by phone or WhatsApp.
@else
All slots are currently taken: your request is on the waiting list. We will contact you if a place becomes available. This message is not a booked appointment.
@endif

FEIRRY Construction Designs — Shaping a brighter tomorrow
@else
Bonjour {{ $lead->name }},

Merci, votre demande Coup de Pouce FEIRRY a bien été reçue.
Référence : {{ $lead->reference }}

@if ($booked)
Votre {{ $lead->path === 'visit' ? 'visite de chantier' : 'consultation' }} est réservée le {{ $when }} (heure de Douala), {{ $where }}.
En cas d'empêchement, ou si FEIRRY doit déplacer le rendez-vous, nous le fixerons avec vous par téléphone ou WhatsApp.
@else
Tous les créneaux sont actuellement réservés : votre demande est placée sur liste d'attente. Nous vous contacterons si une place se libère. Ce message n'est pas un rendez-vous réservé.
@endif

FEIRRY Construction Designs — Shaping a brighter tomorrow
@endif
