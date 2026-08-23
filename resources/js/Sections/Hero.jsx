import { Fragment, useRef } from 'react';
import { motion, useScroll, useTransform } from 'motion/react';
import { cn, EASE, interpolate } from '@/Lib/utils';
import { useReducedMotion, useSite } from '@/Hooks';
import { scrollToAnchor } from '@/Hooks/useSmoothScroll';
import { Button, Icon, Marquee } from '@/Components/Ui';

/* The fallback plate, used until the dashboard sets one. It is served off the
   public disk through the storage symlink, so swapping the file swaps the
   picture — see storage/app/public/heroes/. */
const HERO_IMAGE = '/storage/heroes/home.jpg';

/*
 * Both heroes are split: the type sits on a solid panel and the photograph
 * runs beside it. The two no longer butt against a hard edge — the plate's
 * inner edge dissolves into the panel so the pair reads as one field.
 *
 * The fade is confined to that edge. The body of the photograph keeps its full
 * strength and nothing else is laid over it but a short scrim at the very top,
 * which exists only so the fixed header stays legible where it crosses the
 * plate. Contrast still comes from the composition rather than from dimming
 * the picture — an earlier treatment stacked three full-plate scrims and left
 * the image a smear, which is the thing to avoid here.
 */

/** The scrim under the fixed header. The only thing that touches the image. */
function HeaderScrim() {
    return (
        <div
            aria-hidden="true"
            className="pointer-events-none absolute inset-x-0 top-0 h-44 bg-gradient-to-b from-ink-950/95 via-ink-950/55 to-transparent"
        />
    );
}

/**
 * The seam between the plate and the type panel.
 *
 * Below `lg` the two are stacked, so the join is horizontal and the fade runs
 * up from the bottom of the image; from `lg` they sit side by side and it runs
 * in from the left. It resolves to the panel's own ink at the very edge, so
 * there is no line left where the two meet.
 */
function PlateFade() {
    return (
        <div
            aria-hidden="true"
            className={cn(
                'pointer-events-none absolute inset-x-0 bottom-0 h-1/3',
                'bg-gradient-to-t from-ink-950 via-ink-950/55 to-transparent',
                'lg:inset-x-auto lg:inset-y-0 lg:left-0 lg:h-auto lg:w-2/5 lg:bg-gradient-to-r',
            )}
        />
    );
}

/** The photograph plate, with a restrained parallax drift. */
function HeroPlate({ image, className }) {
    const ref = useRef(null);
    const reduced = useReducedMotion();

    const { scrollYProgress } = useScroll({ target: ref, offset: ['start start', 'end start'] });
    const y = useTransform(scrollYProgress, [0, 1], ['0%', '10%']);

    return (
        <div ref={ref} className={cn('relative overflow-hidden bg-ink-900', className)}>
            <motion.img
                src={image}
                alt=""
                fetchPriority="high"
                style={reduced ? undefined : { y }}
                className="absolute inset-0 h-[112%] w-full object-cover"
            />
            <HeaderScrim />
            <PlateFade />
        </div>
    );
}

export function Hero({ partners = [] }) {
    const { t, site, hero } = useSite();

    /* The admin's copy wins; the translation files stay as the fallback, so
       clearing a field in the dashboard puts the shipped wording back rather
       than leaving the page blank. */
    const year = site.brand?.founded ?? 2009;
    const titleTop = hero?.title ?? t('home.hero_title_1');
    const titleAccent = hero?.title_accent ?? t('home.hero_title_2');
    const lead = interpolate(hero?.lead ?? t('home.hero_lead'), { year });
    const image = hero?.image ?? HERO_IMAGE;

    return (
        <section className="relative isolate overflow-hidden bg-ink-950 text-concrete-50">
            <div className="grid lg:grid-cols-2">
                {/* Type panel. `order` puts the photograph first on a phone, where
                    a stacked hero reads better opening on the image. */}
                <div className="shell-start order-2 flex flex-col justify-center pb-10 pr-5 pt-10 md:pr-10 lg:order-1 lg:min-h-[88svh] lg:py-20 lg:pr-16">
                    {/* Display spacing. Archivo's content box is 1.088em tall
                        (ascent .878 + descent .210) and every word here sits in
                        an overflow-hidden box for the reveal, so leading below
                        that crops the type: at .92 the tails of p/q/g were being
                        shaved and the two lines closed up on each other. 1.09
                        clears the metrics exactly and lets the pair breathe.
                        Tracking eases to -.02em — still optically tightened for
                        72px extrabold, but the words no longer touch. */}
                    <h1 className="text-[clamp(2.5rem,5vw,4.5rem)] leading-[1.09] tracking-[-0.02em]">
                        <HeroLine text={titleTop} delay={0.3} />
                        {titleAccent && (
                            <HeroLine text={titleAccent} delay={0.42} className="text-brand" />
                        )}
                    </h1>

                    <motion.p
                        initial={{ opacity: 0, y: 22 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.9, delay: 0.65, ease: EASE.outExpo }}
                        className="mt-8 max-w-xl text-base leading-relaxed text-concrete-300 sm:text-lg"
                    >
                        {lead}
                    </motion.p>

                    <motion.div
                        initial={{ opacity: 0, y: 22 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.9, delay: 0.78, ease: EASE.outExpo }}
                        className="mt-10 flex flex-wrap items-center gap-4"
                    >
                        <Button href="/devis" tone="brand" size="lg">
                            {t('home.hero_cta_primary')}
                        </Button>
                        <Button href="/realisations" tone="ghost" size="lg" icon="arrow-right">
                            {t('home.hero_cta_secondary')}
                        </Button>
                    </motion.div>
                </div>

                <HeroPlate
                    image={image}
                    className="order-1 min-h-[44svh] lg:order-2 lg:min-h-full"
                />
            </div>

            {/* Bottom rail: scroll cue + client ticker */}
            <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ duration: 1, delay: 1.05 }}
                className="relative border-t border-ink-800/80"
            >
                <div className="shell flex flex-col gap-6 py-6 lg:flex-row lg:items-center lg:justify-between">
                    <button
                        type="button"
                        onClick={() => scrollToAnchor('#intro', -80)}
                        className="group/scroll flex shrink-0 items-center gap-3 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-concrete-500 transition-colors hover:text-brand"
                    >
                        <span className="flex size-9 items-center justify-center rounded-full border border-ink-700 transition-colors duration-500 group-hover/scroll:border-brand">
                            <Icon
                                name="arrow-down"
                                className="size-3.5 animate-bounce"
                                strokeWidth={2}
                                style={{ animationDuration: '2.4s' }}
                            />
                        </span>
                        {t('common.scroll')}
                    </button>

                    {partners.length > 0 && (
                        <div className="min-w-0 flex-1 lg:max-w-3xl">
                            <Marquee speed={38} itemClassName="px-8">
                                {partners.map((partner) => (
                                    <span
                                        key={partner.id}
                                        className="whitespace-nowrap font-display text-sm font-bold uppercase tracking-[0.08em] text-concrete-600"
                                    >
                                        {partner.name}
                                    </span>
                                ))}
                            </Marquee>
                        </div>
                    )}
                </div>
            </motion.div>
        </section>
    );
}

function HeroLine({ text, delay = 0, className }) {
    const reduced = useReducedMotion();
    const words = String(text).split(' ');

    if (reduced) {
        return <span className={cn('block', className)}>{text}</span>;
    }

    return (
        <span className={cn('block overflow-hidden', className)}>
            <motion.span
                className="block"
                initial="hidden"
                animate="visible"
                variants={{
                    hidden: {},
                    visible: { transition: { staggerChildren: 0.06, delayChildren: delay } },
                }}
            >
                {words.map((word, index) => (
                    <Fragment key={`${word}-${index}`}>
                    {/* The separator sits between the word boxes, never inside
                        one: a trailing space at the end of an inline-block is
                        stripped by white-space processing, which ran the words
                        together. Large type hid it behind the glyph sidebearings. */}
                        {index > 0 && ' '}
                        <span className="inline-block overflow-hidden align-bottom">
                            <motion.span
                                className="inline-block"
                                variants={{
                                    hidden: { y: '108%', skewY: 4 },
                                    visible: {
                                        y: '0%',
                                        skewY: 0,
                                        transition: { duration: 1.15, ease: EASE.outExpo },
                                    },
                                }}
                            >
                                {word}
                            </motion.span>
                        </span>
                    </Fragment>
                ))}
            </motion.span>
        </span>
    );
}

/* ------------------------------------------------------------------ */
/* PageHero: the compact hero used by every inner page.                */
/* ------------------------------------------------------------------ */
export function PageHero({ title, lead, image, breadcrumb, children }) {
    const { hero } = useSite();

    /* Each page still passes its shipped copy and photograph; those are the
       fallback. Anything set against this page in the dashboard takes over,
       which is why no page file had to learn about the hero table. */
    const heading = hero?.title ?? title;
    const standfirst = hero?.lead ?? lead;
    const plate = hero?.image ?? image;

    const panel = (
        <div
            className={cn(
                'order-2 flex flex-col justify-center pb-10 pt-10 lg:order-1 lg:pb-16',
                plate
                    // From `lg` the panel sits beside the photograph rather than
                    // under it, which puts its top edge behind the fixed header.
                    // The panel centres its contents, so a headline long enough
                    // to outgrow the row pushes the breadcrumb up into the nav —
                    // the French titles wrap a line further than the English and
                    // hit it first. Clearing the bar here rather than nudging the
                    // breadcrumb keeps the block centred in what is left.
                    ? 'shell-start pr-5 md:pr-10 lg:pr-16 lg:pt-32'
                    // With no photograph there is nothing to sit beside, so the
                    // panel takes the full width on the ordinary shell. The two
                    // insets must not both apply, or the text lands off the grid.
                    : 'shell col-span-full pb-14 pt-32 lg:pt-36',
            )}
        >
            {breadcrumb && (
                <motion.nav
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.7, delay: 0.1, ease: EASE.outExpo }}
                    aria-label="Fil d'Ariane"
                    className="mb-7 flex flex-wrap items-center gap-2 font-mono text-[0.625rem] uppercase tracking-[0.16em] text-concrete-500"
                >
                    {breadcrumb}
                </motion.nav>
            )}

            {/* Same setting as the home hero with a little more leading: these
                titles are longer and usually run to two or three lines. */}
            <h1 className="text-[clamp(2.125rem,4.4vw,4rem)] leading-[1.12] tracking-[-0.018em]">
                <HeroLine text={heading} delay={0.28} />
            </h1>

            {standfirst && (
                <motion.p
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.9, delay: 0.55, ease: EASE.outExpo }}
                    className="mt-7 max-w-xl text-base leading-relaxed text-concrete-300 sm:text-lg"
                >
                    {standfirst}
                </motion.p>
            )}

            {children && (
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.9, delay: 0.7, ease: EASE.outExpo }}
                    className="mt-9"
                >
                    {children}
                </motion.div>
            )}
        </div>
    );

    return (
        <section className="relative isolate overflow-hidden bg-ink-950 text-concrete-50">
            <div className={cn('grid', plate && 'lg:min-h-[62svh] lg:grid-cols-2')}>
                {panel}
                {plate && (
                    <HeroPlate
                        image={plate}
                        className="order-1 min-h-[34svh] lg:order-2 lg:min-h-full"
                    />
                )}
            </div>
        </section>
    );
}
