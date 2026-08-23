import { useId } from 'react';
import { Link } from '@/Components/Link';
import { cn } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { WipeText } from '@/Components/Motion';

/*
 * The mark and the logotype are traced from the supplied Feirry artwork, so
 * these are the real letterforms rather than an approximation set in Archivo.
 * Both fill with currentColor: one copy of the geometry then serves the header,
 * the footer and the preloader, on light ground and dark alike.
 */

const MARK_PATH = 'M0 0H819V587L408 149L175 409L539 819H0Z';

const WORDMARK_PATH =
    'M4173 0L4889 9L4921 17L4968 35L5009 58L5036 78L5068 108L5091 136L5111 167L5128 202L5141 241L5148 278L5150 303L5148 350L5140 390L5131 417L5115 451L5096 481L5076 506L5036 546L5020 557L4979 578L4972 584L4969 593L4974 604L4990 625L5083 723L5101 745L5106 756L5035 818L5031 819L4939 821L4849 818L4842 815L4821 792L4728 655L4706 627L4692 614L4438 614L4438 818L4173 818ZM3110 0L3826 9L3858 17L3896 31L3933 50L3974 79L4002 105L4030 139L4049 169L4064 200L4077 238L4084 272L4086 290L4085 348L4077 389L4067 419L4055 445L4032 482L4011 508L3986 533L3976 540L3968 550L3917 577L3908 585L3906 593L3909 601L3925 623L4018 721L4040 748L4043 756L3973 817L3968 819L3876 821L3786 818L3774 811L3757 791L3667 658L3641 625L3629 614L3375 614L3375 818L3110 818ZM1028 0L1954 0L1954 205L1304 205L1297 215L1293 247L1294 307L1851 307L1851 511L1306 511L1298 519L1294 537L1294 613L1954 614L1954 818L1028 818ZM2057 0L2982 0L2982 205L2656 205L2656 613L2982 614L2982 818L2057 818L2057 614L2382 614L2382 205L2057 205ZM192 0L926 0L926 205L274 206L270 212L266 229L266 307L823 307L823 511L279 511L266 524L266 818L0 818L0 207L37 162L181 14ZM5089 0L5439 0L5457 12L5507 56L5724 262L5758 291L5778 305L5784 302L6113 0L6469 1L6397 73L6301 164L6018 418L5912 516L5912 818L5647 818L5647 516L5108 27L5094 18L5089 12ZM4439 205L4438 409L4814 409L4842 388L4869 357L4875 336L4876 307L4867 269L4855 246L4845 233L4831 220L4811 209L4796 205ZM3376 205L3375 409L3750 409L3771 395L3797 369L3808 351L3813 326L3813 309L3810 289L3802 265L3793 248L3780 231L3765 218L3748 209L3733 205Z';

/* ------------------------------------------------------------------ */
/* Polish: a shallow emboss and an occasional glint of light.          */
/*                                                                     */
/* Both are cut from the artwork rather than laid over it, so the      */
/* treatment survives every backdrop the identity lands on — the near  */
/* black hero, the frosted light bar, the footer — with no per-ground  */
/* adjustment. The bevel is the glyph minus a copy of itself nudged a  */
/* fraction down, which leaves the lit top edge, and the same trick    */
/* inverted for the shadow under the lower edge. Masking rather than   */
/* an SVG filter keeps it crisp: feSpecularLighting blurs, and at the  */
/* twenty pixels the logotype sets in the header that reads as fuzz.   */
/*                                                                     */
/* The glint is a soft band clipped to the glyph. It is deliberately   */
/* off the shape for most of its cycle; see the keyframes in app.css.  */
/* ------------------------------------------------------------------ */
function Glyph({ path, viewBox, width, height, bevel, sheenDelay = 0, className, polished = false }) {
    const uid = useId();
    const id = (part) => `${uid}-${part}`;

    if (!polished) {
        return (
            <svg viewBox={viewBox} className={className} fill="currentColor" aria-hidden="true">
                <path d={path} />
            </svg>
        );
    }

    return (
        <svg viewBox={viewBox} className={className} aria-hidden="true">
            <defs>
                <path id={id('glyph')} d={path} />

                <clipPath id={id('clip')}>
                    <use href={`#${id('glyph')}`} />
                </clipPath>

                {/* Shape minus itself shifted down: the top edge is left over. */}
                <mask id={id('lift')}>
                    <use href={`#${id('glyph')}`} fill="#fff" />
                    <use href={`#${id('glyph')}`} fill="#000" transform={`translate(0 ${bevel})`} />
                </mask>

                <mask id={id('drop')}>
                    <use href={`#${id('glyph')}`} fill="#fff" />
                    <use href={`#${id('glyph')}`} fill="#000" transform={`translate(0 ${-bevel})`} />
                </mask>

                <linearGradient id={id('sheen')} x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0.3" stopColor="#fff" stopOpacity="0" />
                    <stop offset="0.5" stopColor="#fff" stopOpacity="0.55" />
                    <stop offset="0.7" stopColor="#fff" stopOpacity="0" />
                </linearGradient>
            </defs>

            <use href={`#${id('glyph')}`} fill="currentColor" />
            <use href={`#${id('glyph')}`} fill="#fff" opacity="0.34" mask={`url(#${id('lift')})`} />
            <use href={`#${id('glyph')}`} fill="#000" opacity="0.26" mask={`url(#${id('drop')})`} />

            <g clipPath={`url(#${id('clip')})`}>
                {/* Raked slightly off vertical, the way a highlight crosses a
                    raised surface. The skew sits on the wrapper so the CSS
                    transform underneath is left free to carry the travel. */}
                <g transform="skewX(-14)">
                    <rect
                        x={0}
                        y={-height}
                        width={width * 0.5}
                        height={height * 3}
                        fill={`url(#${id('sheen')})`}
                        className="animate-logo-sheen"
                        style={{
                            '--sheen-from': `${-width}px`,
                            '--sheen-to': `${width * 1.5}px`,
                            '--sheen-duration': '9s',
                            animationDelay: `${sheenDelay}s`,
                        }}
                    />
                </g>
            </g>
        </svg>
    );
}

/** The chevron mark. Its notch is knocked out, so it takes the backdrop. */
export function LogoMark({ className, polished = false }) {
    return (
        <Glyph
            path={MARK_PATH}
            viewBox="0 0 819 819"
            width={819}
            height={819}
            bevel={13}
            className={className}
            polished={polished}
        />
    );
}

/** The FEIRRY logotype. */
export function LogoWordmark({ className, polished = false }) {
    return (
        <Glyph
            path={WORDMARK_PATH}
            viewBox="0 0 6469 821"
            width={6469}
            height={821}
            bevel={26}
            // The mark is to its left, so the light reaches the logotype a
            // beat later and the pair reads as one surface rather than two
            // things glinting at once.
            sheenDelay={0.45}
            className={className}
            polished={polished}
        />
    );
}

/*
 * The slogan under the logotype. It is the identity's voice rather than one of
 * the site's technical labels, so it is set in the display italic instead of
 * the mono used for eyebrows and metadata. The movement is WipeText's, the
 * same treatment the nav links take, so the whole bar behaves as one object.
 */
const TAGLINE_TYPE =
    'block whitespace-nowrap pr-[0.3em] font-display text-[0.625rem] font-bold italic uppercase leading-none tracking-[0.18em]';

export function LogoTagline({ text, tone = 'dark', className }) {
    // On the compact header the line sits on #EFEFEF, where the token sheet's
    // rule applies: plain brand orange clears 3:1 but not 4.5:1 at text size,
    // so the wipe uses the deep step there. The resting grey goes one step
    // darker than the old mono line for the same reason — bold at 10px wants
    // more than concrete-500 gives it against the light bar.
    return (
        <WipeText
            text={text}
            typeClassName={TAGLINE_TYPE}
            restClassName={tone === 'dark' ? 'text-concrete-600' : 'text-concrete-400'}
            wipeClassName={tone === 'dark' ? 'text-brand-deep' : 'text-brand'}
            // The header itself slides down over 0.6s; the slogan waits for it
            // to land rather than arriving mid-flight.
            delay={0.55}
            className={className}
        />
    );
}

/**
 * The header/footer lockup: mark, logotype and locality line. The artwork stays
 * brand orange on every backdrop — that is how the identity is drawn, and the
 * orange clears contrast against both #0B090B and #EFEFEF — so `tone` only
 * picks the weight of the slogan underneath. `tagline` turns that line off
 * where the surrounding layout already carries the slogan itself.
 */
export function Logo({ tone = 'dark', tagline = true, className }) {
    const { site } = useSite();

    return (
        <Link
            href="/"
            className={cn(
                // `wipe` drives the slogan's hover fill. The mark used to carry
                // a second group for its own hover sweep; the glint replaced it
                // and runs on its own, so only the one name is left.
                'group/wipe inline-flex items-center gap-3',
                className,
            )}
            aria-label={site.brand?.name}
        >
            <LogoMark polished className="size-12 shrink-0 text-brand" />

            <span className="flex flex-col">
                <LogoWordmark polished className="h-[1.25rem] w-auto text-brand" />
                {tagline && site.brand?.tagline && (
                    <LogoTagline text={site.brand.tagline} tone={tone} className="mt-2" />
                )}
            </span>
        </Link>
    );
}
