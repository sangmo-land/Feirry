import { useId } from 'react';
import { cn } from '@/Lib/utils';

/**
 * Locale flags for the language switcher.
 *
 * Drawn rather than set as emoji: Windows ships no flag glyphs at all, so
 * "🇫🇷" renders there as the bare letters FR and the switcher would read as
 * duplicated text on the client's own machines.
 *
 * Both are drawn into the same 3:2 box so the pair lines up in a row. That is
 * native for the tricolour and a small vertical stretch for the union flag,
 * which is what every icon set that normalises flags to one ratio does.
 */
const FLAG_BOX = 'block h-3 w-[1.125rem] shrink-0 rounded-[1px]';

/** A hairline so the white bands do not bleed into a light backdrop. */
function Edge() {
    return (
        <rect
            x="1"
            y="1"
            width="58"
            height="38"
            fill="none"
            stroke="currentColor"
            strokeOpacity="0.28"
            strokeWidth="2"
        />
    );
}

export function FlagFr({ className }) {
    return (
        <svg viewBox="0 0 60 40" className={cn(FLAG_BOX, className)} aria-hidden="true">
            <rect width="20" height="40" fill="#002395" />
            <rect x="20" width="20" height="40" fill="#ffffff" />
            <rect x="40" width="20" height="40" fill="#ED2939" />
            <Edge />
        </svg>
    );
}

export function FlagEn({ className }) {
    const clip = useId();

    return (
        <svg viewBox="0 0 60 40" className={cn(FLAG_BOX, className)} aria-hidden="true">
            {/* The four quadrants that counterchange St Patrick's saltire: the
                red arm is offset within the white one, clockwise from the
                hoist, and only a per-quadrant clip produces that. */}
            <clipPath id={clip}>
                <path d="M30 20 h30 v20 z M30 20 v20 h-30 z M30 20 h-30 v-20 z M30 20 v-20 h30 z" />
            </clipPath>

            <rect width="60" height="40" fill="#012169" />
            <path d="M0 0 L60 40 M60 0 L0 40" stroke="#ffffff" strokeWidth="8" />
            <path
                d="M0 0 L60 40 M60 0 L0 40"
                clipPath={`url(#${clip})`}
                stroke="#C8102E"
                strokeWidth="5.33"
            />
            <path d="M30 0 v40 M0 20 h60" stroke="#ffffff" strokeWidth="13.33" />
            <path d="M30 0 v40 M0 20 h60" stroke="#C8102E" strokeWidth="8" />
            <Edge />
        </svg>
    );
}

const FLAGS = { fr: FlagFr, en: FlagEn };

/** Resolves a locale code to its flag; unknown codes simply render nothing. */
export function Flag({ code, className }) {
    const Component = FLAGS[String(code ?? '').toLowerCase()];

    return Component ? <Component className={className} /> : null;
}
