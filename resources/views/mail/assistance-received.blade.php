@if ($lead->locale === 'en')
Hello {{ $lead->name }},

Thank you, your Coup de Pouce FEIRRY request has been received.
Reference: {{ $lead->reference }}

Our team will review it and contact you to confirm a possible {{ $lead->path === 'visit' ? 'site visit' : 'consultation' }}, depending on availability. This message is not a booked appointment.

FEIRRY Construction Designs — Shaping a brighter tomorrow
@else
Bonjour {{ $lead->name }},

Merci, votre demande Coup de Pouce FEIRRY a bien été reçue.
Référence : {{ $lead->reference }}

Notre équipe examinera votre demande et vous contactera pour confirmer une éventuelle {{ $lead->path === 'visit' ? 'visite' : 'consultation' }} selon les disponibilités. Ce message n'est pas un rendez-vous réservé.

FEIRRY Construction Designs — Shaping a brighter tomorrow
@endif
