<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Coup de Pouce — automatic booking (CDC correctif CAMP-02, décision FEIRRY du 18/11/2026).
 *
 * Two unique keys make over-booking impossible at database level, whatever
 * the number of simultaneous requests:
 *   slot_key — one request per time slot;
 *   seat_key — one request per numbered seat of the week (quota).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {
            $table->string('slot_key', 80)->nullable()->unique();
            $table->string('seat_key', 80)->nullable()->unique();
        });

        // The pilot edition said "sending a request does not book a slot".
        // Booking is now automatic: the sentence is replaced, but only where
        // the installed wording is still in place (an edited text is kept).
        $swap = [
            'terms_fr' => [
                'Les demandes sont étudiées par l’équipe FEIRRY : places limitées, sélection et confirmation par FEIRRY. L’envoi d’une demande ne réserve aucun créneau.',
                'Places limitées : vous choisissez votre créneau en ligne et il vous est réservé dès l’envoi de la demande. FEIRRY vous recontacte si le créneau doit être déplacé ou si la demande sort du cadre du programme.',
            ],
            'terms_en' => [
                'Requests are reviewed by the FEIRRY team: places are limited, subject to selection and confirmation by FEIRRY. Sending a request does not book a slot.',
                'Places are limited: you choose your slot online and it is booked as soon as you send your request. FEIRRY will contact you if the slot has to be moved or if the request falls outside the programme.',
            ],
        ];

        foreach (DB::table('campaigns')->get() as $campaign) {
            $update = [];
            foreach ($swap as $column => [$old, $new]) {
                if (str_contains((string) $campaign->{$column}, $old)) {
                    $update[$column] = str_replace($old, $new, $campaign->{$column});
                }
            }
            if ($update) {
                DB::table('campaigns')->where('id', $campaign->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {
            $table->dropUnique(['slot_key']);
            $table->dropUnique(['seat_key']);
            $table->dropColumn(['slot_key', 'seat_key']);
        });
    }
};
