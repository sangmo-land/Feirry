<?php

namespace App\Support;

use App\Models\AssistanceRequest;

/**
 * Ready-made applicant messages (CDC Coup de Pouce §14). They are copied and
 * sent by a FEIRRY agent; nothing here is sent automatically. No message
 * promises a slot before the request is actually scheduled.
 */
class CdpMessages
{
    public static function labels(): array
    {
        return [
            'received' => 'Accusé de réception',
            'incomplete' => 'Dossier incomplet',
            'waiting' => 'Liste d’attente',
            'scheduled' => 'Confirmation de rendez-vous',
            'report_sent' => 'Envoi du rapport',
            'out_of_scope' => 'Hors périmètre (refus motivé)',
            'closed' => 'Clôture',
        ];
    }

    public static function defaultFor(?string $status): string
    {
        return match ($status) {
            'incomplete' => 'incomplete',
            'waiting' => 'waiting',
            'scheduled' => 'scheduled',
            'done', 'report_sent' => 'report_sent',
            'out_of_scope' => 'out_of_scope',
            'closed' => 'closed',
            default => 'received',
        };
    }

    public static function render(?string $key, AssistanceRequest $r): string
    {
        $what = $r->isVisit() ? 'visite de chantier' : 'consultation de dossier';
        $hello = 'Bonjour '.trim(explode(' ', (string) $r->name)[0]).',';
        $sign = "\n\nL’équipe FEIRRY Construction Designs\nRéférence : {$r->reference}";
        $when = $r->scheduled_at?->locale('fr')->isoFormat('dddd D MMMM YYYY [à] HH[h]mm') ?? '[date et heure]';
        $mode = ['office' => 'au bureau FEIRRY', 'video' => 'en visioconférence (le lien vous sera envoyé)', 'site' => 'sur votre chantier'][$r->scheduled_mode] ?? '[lieu ou mode]';

        $body = match ($key) {
            'incomplete' => "Merci pour votre demande de {$what} dans le cadre du Coup de Pouce FEIRRY. Pour l’étudier, il nous manque : [élément manquant]. Vous pouvez nous l’envoyer en répondant à ce message ou le déposer au bureau.",
            'waiting' => "Merci pour votre demande de {$what}. Les places de cette période sont pourvues : votre demande est placée sur liste d’attente. Nous vous recontacterons si une place se libère.",
            'scheduled' => "Votre demande de {$what} a été retenue. Rendez-vous le {$when}, {$mode}. Merci de confirmer votre présence en répondant à ce message. ".($r->isVisit() ? 'Une personne majeure devra être présente pour l’accès au chantier.' : 'Pensez à apporter vos plans et documents.'),
            'report_sent' => "Suite à notre {$what}, vous trouverez ci-joint le rapport préliminaire. Il s’agit d’une première orientation technique, qui ne remplace pas une étude ou une mission de suivi. Nous restons disponibles pour en parler.",
            'out_of_scope' => "Merci pour votre demande. Après étude, nous ne pouvons pas la retenir dans le cadre de cette édition du Coup de Pouce : [motif]. Vous pouvez néanmoins décrire votre projet à FEIRRY sur feirry.org/mon-projet.",
            'closed' => "Votre demande Coup de Pouce est désormais clôturée. Merci de votre confiance. Pour toute suite à donner à votre projet, FEIRRY reste à votre disposition.",
            default => "Nous avons bien reçu votre demande de {$what} dans le cadre du Coup de Pouce FEIRRY. Notre équipe l’examine et vous contactera pour confirmer une éventuelle date selon les disponibilités. Cette réponse n’est pas un rendez-vous réservé.",
        };

        return $hello."\n\n".$body.$sign;
    }
}
