<?php

namespace App\Support;

use App\Models\AssistanceRequest;
use App\Models\Campaign;
use Carbon\CarbonImmutable;

/**
 * Coup de Pouce — bookable slots (CDC correctif CAMP-02).
 *
 * The grid is computed, never stored: edition dates × configured week days ×
 * configured times. A slot is taken when a request holds its key; a week is
 * full when the edition's weekly quota of seats is used. Both are enforced
 * again by unique indexes when the request is written.
 */
class CdpSlots
{
    public const FORMAT = 'Y-m-d H:i';

    /** Weekly quota for a path: the places the edition announces. */
    public static function quota(Campaign $campaign, string $path): int
    {
        return max(0, (int) ($path === 'visit' ? $campaign->public_visits : $campaign->public_consultations));
    }

    public static function slotKey(Campaign $campaign, string $path, string $slot): string
    {
        return "{$campaign->id}|{$path}|{$slot}";
    }

    public static function seatKey(Campaign $campaign, string $path, CarbonImmutable $at, int $seat): string
    {
        return "{$campaign->id}|{$path}|{$at->format('o-\WW')}|{$seat}";
    }

    public static function parse(string $slot): ?CarbonImmutable
    {
        try {
            $at = CarbonImmutable::createFromFormat(self::FORMAT, $slot, config('app.timezone'));
        } catch (\Throwable) {
            return null;
        }

        return $at && $at->format(self::FORMAT) === $slot ? $at->startOfMinute() : null;
    }

    /** Every slot of the edition that can still be reached, booked or not. */
    public static function grid(Campaign $campaign, string $path): array
    {
        $config = config("site.cdp.slots.{$path}");
        if (! $config || ! $campaign->starts_at || ! $campaign->ends_at || static::quota($campaign, $path) === 0) {
            return [];
        }

        $earliest = CarbonImmutable::now()->addHours((int) config('site.cdp.slots.lead_hours', 24));
        $day = CarbonImmutable::parse($campaign->starts_at->toDateString());
        $last = CarbonImmutable::parse($campaign->ends_at->toDateString());
        $slots = [];

        for (; $day->lte($last); $day = $day->addDay()) {
            if (! in_array($day->isoWeekday(), $config['days'], true)) {
                continue;
            }
            foreach ($config['times'] as $time) {
                $at = $day->setTimeFromTimeString($time);
                if ($at->gte($earliest)) {
                    $slots[] = $at;
                }
            }
        }

        return $slots;
    }

    /**
     * What the public form shows: days and times, free or not. Never a count
     * (the number of requests received is not public).
     */
    public static function publicList(Campaign $campaign, string $path): array
    {
        $grid = static::grid($campaign, $path);
        if (! $grid) {
            return [];
        }

        $booked = AssistanceRequest::where('campaign_id', $campaign->id)->where('path', $path);
        $taken = (clone $booked)->whereNotNull('slot_key')->pluck('slot_key')->flip();
        $quota = static::quota($campaign, $path);
        $weeks = (clone $booked)->whereNotNull('seat_key')->pluck('seat_key')
            ->countBy(fn (string $key) => explode('|', $key)[2] ?? '');

        $days = [];
        foreach ($grid as $at) {
            $slot = $at->format(self::FORMAT);
            $weekFull = ($weeks[$at->format('o-\WW')] ?? 0) >= $quota;
            $days[$at->toDateString()][] = [
                'value' => $slot,
                'time' => $at->format('H:i'),
                'free' => ! $weekFull && ! $taken->has(static::slotKey($campaign, $path, $slot)),
            ];
        }

        return collect($days)->map(fn (array $times, string $date) => ['date' => $date, 'times' => $times])->values()->all();
    }

    public static function anyFree(Campaign $campaign, string $path): bool
    {
        foreach (static::publicList($campaign, $path) as $day) {
            foreach ($day['times'] as $time) {
                if ($time['free']) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function isBookable(Campaign $campaign, string $path, string $slot): bool
    {
        foreach (static::publicList($campaign, $path) as $day) {
            foreach ($day['times'] as $time) {
                if ($time['value'] === $slot) {
                    return $time['free'];
                }
            }
        }

        return false;
    }
}
