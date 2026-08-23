import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { Button, Eyebrow, Icon, Marquee, Section, SectionHeading } from '@/Components/Ui';
import { StatBlock } from '@/Components/Cards';
import { useZoom } from '@/Components/Media';
import { ClipReveal, Reveal, SplitText } from '@/Components/Motion';

/* ------------------------------------------------------------------ */
/* StatsBand                                                           */
/* ------------------------------------------------------------------ */
export function StatsBand({ tone = 'light', eyebrow, className }) {
    const { site, t } = useSite();
    const stats = site.stats ?? [];

    if (!stats.length) return null;

    return (
        <div className={cn('shell', className)}>
            {eyebrow !== false && <Eyebrow tone={tone === 'light' ? 'dark' : 'light'}>{eyebrow ?? t('home.stats_eyebrow')}</Eyebrow>}
            <div className="mt-10 grid gap-x-10 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                {stats.map((stat, index) => (
                    <StatBlock key={stat.label} stat={stat} index={index} tone={tone === 'light' ? 'dark' : 'light'} />
                ))}
            </div>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* CtaBand: the closing call to action, used on almost every page.     */
/* Brand orange, so it reads as a call to action rather than as a       */
/* second footer stacked on the real one directly beneath it.          */
/* ------------------------------------------------------------------ */
export function CtaBand({ title, lead, primary, secondary }) {
    const { t } = useSite();

    return (
        <section className="noise-overlay relative overflow-hidden bg-brand py-16 text-ink-950 sm:py-20">
            <div className="blueprint-grid-dark absolute inset-0 opacity-70" />

            <div className="shell relative">
                <div className="max-w-4xl">
                    <Reveal y={16}>
                        <Eyebrow tone="brand">{t('nav.contact')}</Eyebrow>
                    </Reveal>

                    <h2 className="mt-6 text-[clamp(2.25rem,6.5vw,5.5rem)] leading-[0.92] tracking-[-0.045em]">
                        <SplitText text={title ?? t('home.cta_title')} />
                    </h2>

                    <Reveal delay={0.15}>
                        <p className="mt-8 max-w-2xl text-base leading-relaxed text-ink-900/75 sm:text-lg">
                            {lead ?? t('home.cta_lead')}
                        </p>
                    </Reveal>

                    <Reveal delay={0.25} className="mt-12 flex flex-wrap gap-4">
                        <Button href={primary?.href ?? '/devis'} tone="solid" size="lg">
                            {primary?.label ?? t('home.cta_button')}
                        </Button>
                        <Button href={secondary?.href ?? '/contact'} tone="outline" size="lg" icon="arrow-right">
                            {secondary?.label ?? t('nav.contact')}
                        </Button>
                    </Reveal>
                </div>
            </div>
        </section>
    );
}

/* ------------------------------------------------------------------ */
/* PartnerStrip                                                        */
/* ------------------------------------------------------------------ */
export function PartnerStrip({ partners = [], title, tone = 'light' }) {
    const { t } = useSite();

    if (!partners.length) return null;

    const dark = tone === 'dark';

    return (
        <div className={cn('border-y py-8', dark ? 'border-ink-800 bg-ink-900' : 'border-concrete-200 bg-concrete-100')}>
            <div className="shell">
                <Reveal>
                    <p
                        className={cn(
                            'text-center font-mono text-[0.625rem] uppercase tracking-[0.2em]',
                            dark ? 'text-concrete-600' : 'text-concrete-500',
                        )}
                    >
                        {title ?? t('home.trusted_by')}
                    </p>
                </Reveal>
            </div>

            <Marquee speed={46} className="mt-8" itemClassName="px-10">
                {partners.map((partner) => (
                    <span
                        key={partner.id}
                        className={cn(
                            'whitespace-nowrap font-display text-lg font-bold uppercase tracking-[0.04em] transition-colors duration-500 sm:text-2xl',
                            dark ? 'text-ink-700 hover:text-concrete-400' : 'text-concrete-300 hover:text-ink-800',
                        )}
                    >
                        {partner.name}
                    </span>
                ))}
            </Marquee>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* ValuesGrid                                                          */
/* ------------------------------------------------------------------ */
const VALUE_KEYS = [
    { key: 'integrity', icon: 'scale' },
    { key: 'safety', icon: 'shield-check' },
    { key: 'quality', icon: 'badge-check' },
    { key: 'proximity', icon: 'handshake' },
];

export function ValuesGrid({ tone = 'light' }) {
    const { t } = useSite();
    const dark = tone === 'dark';

    return (
        <div className="grid gap-px overflow-hidden border border-concrete-200 bg-concrete-200 sm:grid-cols-2 lg:grid-cols-4">
            {VALUE_KEYS.map((value, index) => (
                <motion.div
                    key={value.key}
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, amount: 0.3 }}
                    transition={{ duration: 0.85, delay: index * 0.08, ease: EASE.outExpo }}
                    className={cn(
                        'group/value relative overflow-hidden p-8 sm:p-10',
                        dark ? 'bg-ink-900' : 'bg-concrete-50',
                    )}
                >
                    <span className="pointer-events-none absolute inset-x-0 bottom-0 h-0 bg-brand/10 transition-[height] duration-[0.9s] [transition-timing-function:var(--ease-out-expo)] group-hover/value:h-full" />

                    <span className="relative flex size-12 items-center justify-center border border-concrete-300 text-ink-900 transition-colors duration-600 group-hover/value:border-brand group-hover/value:bg-brand">
                        <Icon name={value.icon} className="size-5" />
                    </span>

                    <h3 className="relative mt-7 text-xl text-ink-900">{t(`values.${value.key}.title`)}</h3>
                    <p className="relative mt-3 text-sm leading-relaxed text-concrete-600">
                        {t(`values.${value.key}.text`)}
                    </p>

                    <span className="relative mt-8 block font-mono text-[0.625rem] tracking-[0.2em] text-concrete-400">
                        {String(index + 1).padStart(2, '0')}
                    </span>
                </motion.div>
            ))}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* ProcessTimeline: sticky index on the left, steps scroll past.       */
/* ------------------------------------------------------------------ */
export function ProcessTimeline({ steps = [], compact = false }) {
    const { t } = useSite();

    if (!steps.length) return null;

    return (
        <div className="relative">
            <div className="absolute left-[1.4375rem] top-4 hidden h-[calc(100%-4rem)] w-px bg-concrete-200 lg:block" />

            <ol className="space-y-10 lg:space-y-16">
                {steps.map((step, index) => (
                    <motion.li
                        key={step.id}
                        initial={{ opacity: 0, y: 40 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, amount: 0.25 }}
                        transition={{ duration: 0.9, ease: EASE.outExpo }}
                        className="relative grid gap-6 lg:grid-cols-12 lg:gap-12"
                    >
                        <div className="min-w-0 flex items-center gap-5 lg:col-span-4 lg:items-start">
                            <span className="relative z-10 flex size-12 shrink-0 items-center justify-center rounded-full border border-concrete-300 bg-concrete-50 font-mono text-sm font-semibold text-ink-900">
                                {String(index + 1).padStart(2, '0')}
                            </span>

                            <div className="min-w-0 lg:pt-1.5">
                                <h3 className="text-[clamp(1.35rem,2.4vw,2rem)] text-ink-900">{step.title}</h3>
                                {step.duration && (
                                    <p className="mt-2 inline-flex items-center gap-2 font-mono text-[0.625rem] uppercase tracking-[0.16em] text-brand-deep">
                                        <Icon name="clock" className="size-3.5" />
                                        {step.duration}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="min-w-0 lg:col-span-8 lg:pl-0">
                            <p className="max-w-2xl text-sm leading-relaxed text-concrete-600 sm:text-base">
                                {step.description}
                            </p>

                            {!compact && step.deliverables?.length > 0 && (
                                <div className="mt-7">
                                    <span className="font-mono text-[0.625rem] uppercase tracking-[0.18em] text-concrete-400">
                                        {t('process.deliverables')}
                                    </span>
                                    <ul className="mt-4 grid gap-2 sm:grid-cols-2">
                                        {step.deliverables.map((item) => (
                                            <li
                                                key={item}
                                                className="flex items-start gap-2.5 text-sm text-concrete-700"
                                            >
                                                <Icon
                                                    name="check"
                                                    className="mt-0.5 size-4 shrink-0 text-brand-deep"
                                                    strokeWidth={2.4}
                                                />
                                                {item}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </motion.li>
                ))}
            </ol>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* SplitFeature: image on one side, copy on the other, with parallax.  */
/* ------------------------------------------------------------------ */
export function SplitFeature({
    eyebrow,
    title,
    children,
    image,
    imageAlt = '',
    reverse = false,
    action,
    stat,
}) {
    const zoom = useZoom(image, imageAlt);

    return (
        <div className={cn('grid gap-12 lg:grid-cols-2 lg:items-center lg:gap-20')}>
            {/* The badge sits outside ClipReveal: its inner wrapper clips
                overflow, which would swallow the badge's negative offsets. */}
            <div className={cn('relative', reverse && 'lg:order-2')}>
                <ClipReveal direction={reverse ? 'right' : 'left'}>
                    <div className="relative aspect-[4/5] overflow-hidden bg-ink-850 sm:aspect-[4/3] lg:aspect-[4/5]">
                        <img src={image} alt={imageAlt} loading="lazy" className="h-full w-full object-cover" />
                        {zoom.trigger}
                    </div>
                </ClipReveal>
                {zoom.overlay}

                {stat && (
                    <motion.div
                        initial={{ opacity: 0, y: 24 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, amount: 0.5 }}
                        transition={{ duration: 0.8, delay: 0.35, ease: EASE.outExpo }}
                        className={cn(
                            'absolute -bottom-8 w-56 border border-concrete-200 bg-concrete-50 p-6 shadow-[var(--shadow-panel)]',
                            reverse ? 'left-0 lg:-left-10' : 'right-0 lg:-right-10',
                        )}
                    >
                        <span className="block font-display text-4xl font-black tracking-tight text-ink-900">
                            {stat.value}
                            <span className="text-brand">{stat.suffix}</span>
                        </span>
                        <span className="mt-2 block font-mono text-[0.625rem] uppercase leading-relaxed tracking-[0.14em] text-concrete-500">
                            {stat.label}
                        </span>
                    </motion.div>
                )}
            </div>

            <div className={cn(reverse && 'lg:order-1')}>
                {eyebrow && (
                    <Reveal y={14}>
                        <Eyebrow>{eyebrow}</Eyebrow>
                    </Reveal>
                )}

                <h2 className="mt-5 text-[clamp(1.85rem,4vw,3.25rem)] text-ink-900">
                    <SplitText text={title} />
                </h2>

                <Reveal delay={0.12}>
                    <div className="mt-7 space-y-5 text-sm leading-relaxed text-concrete-600 sm:text-base">
                        {children}
                    </div>
                </Reveal>

                {action && (
                    <Reveal delay={0.2} className="mt-10">
                        {action}
                    </Reveal>
                )}
            </div>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* Breadcrumb helper                                                   */
/* ------------------------------------------------------------------ */
export function Crumb({ href, children, last = false }) {
    if (last || !href) {
        return <span className="text-concrete-300">{children}</span>;
    }

    return (
        <>
            <Link href={href} className="transition-colors hover:text-brand">
                {children}
            </Link>
            <span className="text-concrete-700">/</span>
        </>
    );
}

export { Section, SectionHeading };
