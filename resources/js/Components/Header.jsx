import { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Link } from '@/Components/Link';
import { AnimatePresence, motion, useMotionValueEvent, useScroll } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { useLocalePath, useSite } from '@/Hooks';
import { Logo } from '@/Components/Logo';
import { Flag } from '@/Lib/flags';
import { Button } from '@/Components/Ui';
import { WipeText } from '@/Components/Motion';

function LocaleSwitcher({ tone = 'dark', className }) {
    const { locale, locales } = useSite();
    const { url } = usePage();
    const localize = useLocalePath();

    return (
        <div className={cn('flex items-center gap-1 font-mono text-[0.6875rem] tracking-[0.14em]', className)}>
            {Object.entries(locales).map(([code, meta], index) => {
                const current = code === locale;

                return (
                    <span key={code} className="flex items-center">
                        {index > 0 && (
                            <span className={tone === 'dark' ? 'text-concrete-300' : 'text-ink-600'}>/</span>
                        )}
                        <Link
                            // The same page in the other language, not an
                            // endpoint that sets a flag: the pair are real,
                            // shareable addresses now.
                            href={localize(url, code)}
                            localize={false}
                            preserveScroll
                            hrefLang={code}
                            aria-current={current ? 'true' : undefined}
                            className={cn(
                                'group/locale flex items-center gap-1.5 px-1.5 py-1 uppercase transition-colors duration-300',
                                current
                                    ? 'text-brand'
                                    : tone === 'dark'
                                      ? 'text-concrete-500 hover:text-ink-900'
                                      : 'text-concrete-300 hover:text-concrete-50',
                            )}
                        >
                            {/* The flag is decorative — the code beside it is
                                what names the link. Held back while the locale
                                is not the active one so two saturated flags
                                never sit shouting side by side in the bar. */}
                            <Flag
                                code={code}
                                className={cn(
                                    'transition-opacity duration-300',
                                    current ? 'opacity-100' : 'opacity-50 group-hover/locale:opacity-100',
                                )}
                            />
                            {meta.short}
                        </Link>
                    </span>
                );
            })}
        </div>
    );
}

const NAV_TYPE =
    'block whitespace-nowrap font-mono text-[0.6875rem] font-semibold uppercase leading-none tracking-[0.12em]';

/* The resting colour of a link. The active page keeps the strong step on both
   grounds — the brand rule underneath it is the marker, the weight of the type
   only has to stop the label receding next to its neighbours. */
function navRest(tone, active) {
    if (active) return tone === 'dark' ? 'text-ink-900' : 'text-concrete-50';

    return tone === 'dark' ? 'text-concrete-600' : 'text-concrete-300';
}

const NAV = [
    { key: 'about', href: '/entreprise' },
    { key: 'services', href: '/metiers' },
    { key: 'projects', href: '/realisations' },
    { key: 'process', href: '/methode' },
    { key: 'team', href: '/equipe' },
    { key: 'blog', href: '/actualites' },
    { key: 'careers', href: '/carrieres' },
];

export function Header() {
    const { t } = useSite();
    const { url } = usePage();
    const localize = useLocalePath();
    const { scrollY } = useScroll();
    const [open, setOpen] = useState(false);
    // Every page opens on a full-bleed dark hero, so the bar starts transparent
    // with light type and only flips to the frosted light treatment once the
    // visitor has scrolled past it.
    const [compact, setCompact] = useState(false);
    const tone = compact ? 'dark' : 'light';

    // The bar is always on screen, so the only thing scroll decides is whether
    // it wears the frosted treatment. Reading it through a motion value keeps
    // the header off the render path: this runs every frame but only the single
    // flip below reaches React, and the hysteresis band stops that flip
    // oscillating when Lenis settles on a fractional offset near the threshold.
    useMotionValueEvent(scrollY, 'change', (y) => {
        setCompact((value) => (value ? y > 24 : y > 48));
    });

    // A reload that restores its scroll position fires no change event, so the
    // first paint has to take its state from the live offset.
    useEffect(() => {
        setCompact(window.scrollY > 48);
    }, []);

    useEffect(() => router.on('navigate', () => setOpen(false)), []);

    useEffect(() => {
        document.body.style.overflow = open ? 'hidden' : '';

        if (window.__lenis) {
            if (open) window.__lenis.stop();
            else window.__lenis.start();
        }

        return () => {
            document.body.style.overflow = '';
        };
    }, [open]);

    // `url` carries the address actually in the bar — English pages arrive
    // here as /en/company — so the French href each link is written with has
    // to be rendered into the active language before they can be compared.
    // The query string goes first, or a filtered listing stops matching.
    const path = url.split(/[?#]/)[0];
    const isActive = (href) => {
        const target = localize(href);

        return path === target || (href !== '/' && path.startsWith(`${target}/`));
    };

    return (
        <>
            <motion.header
                initial={{ y: -100 }}
                animate={{ y: 0 }}
                transition={{ duration: 0.6, ease: EASE.outExpo }}
                className={cn(
                    // The header owns a stacking context, so the toggle inside it
                    // cannot out-rank the menu overlay on its own: the whole bar
                    // has to sit above the overlay for the close button to work.
                    'fixed inset-x-0 top-0 transition-[background-color,box-shadow,padding] duration-500',
                    open ? 'z-60' : 'z-50',
                    compact && !open
                        ? 'bg-concrete-50/85 py-4 shadow-[0_1px_0_rgb(0_0_0/0.07)] backdrop-blur-xl'
                        : 'bg-transparent py-7',
                )}
            >
                <div className="shell flex items-center justify-between gap-5">
                    <Logo tone={open ? 'light' : tone} />

                    <nav className="hidden items-center gap-1 xl:flex" aria-label="Navigation principale">
                        {NAV.map((item, index) => {
                            const label = t(`nav.${item.key}`);
                            const active = isActive(item.href);

                            return (
                                <Link
                                    key={item.key}
                                    href={item.href}
                                    // WipeText paints both its layers aria-hidden,
                                    // so the name has to come off the link itself.
                                    aria-label={label}
                                    aria-current={active ? 'page' : undefined}
                                    className="group/wipe relative px-3 py-2"
                                >
                                    <WipeText
                                        text={label}
                                        typeClassName={NAV_TYPE}
                                        restClassName={navRest(tone, active)}
                                        wipeClassName={tone === 'dark' ? 'text-brand-deep' : 'text-brand'}
                                        // The bar assembles left to right: the
                                        // slogan lands at 0.55s, then the links
                                        // follow it across.
                                        delay={0.64 + index * 0.045}
                                    />
                                    {active && (
                                        <motion.span
                                            layoutId="nav-active"
                                            className="absolute inset-x-3 -bottom-0.5 h-0.5 bg-brand"
                                            transition={{ duration: 0.5, ease: EASE.outExpo }}
                                        />
                                    )}
                                </Link>
                            );
                        })}
                    </nav>

                    <div className="flex items-center gap-3 sm:gap-5">
                        <LocaleSwitcher tone={open ? 'light' : tone} className="hidden sm:flex" />

                        <button
                            type="button"
                            onClick={() => setOpen((value) => !value)}
                            aria-label={open ? t('nav.close') : t('nav.menu')}
                            aria-expanded={open}
                            className={cn(
                                'relative flex size-11 items-center justify-center rounded-full border transition-colors duration-500 xl:hidden',
                                open || tone === 'light'
                                    ? 'border-concrete-50/30 hover:border-concrete-50'
                                    : 'border-ink-900/20 hover:border-ink-900',
                            )}
                        >
                            <span className="relative flex h-3 w-5 flex-col justify-between">
                                {[
                                    { open: { rotate: 45, y: 5 }, closed: { rotate: 0, y: 0 } },
                                    { open: { opacity: 0 }, closed: { opacity: 1 } },
                                    { open: { rotate: -45, y: -5 }, closed: { rotate: 0, y: 0 } },
                                ].map((bar, index) => (
                                    <motion.span
                                        key={index}
                                        animate={open ? bar.open : bar.closed}
                                        transition={{ duration: 0.4, ease: EASE.outExpo }}
                                        className={cn(
                                            'block h-0.5 w-full transition-colors duration-500',
                                            open || tone === 'light' ? 'bg-concrete-50' : 'bg-ink-900',
                                        )}
                                    />
                                ))}
                            </span>
                        </button>
                    </div>
                </div>
            </motion.header>

            <MobileMenu open={open} onClose={() => setOpen(false)} isActive={isActive} />
        </>
    );
}

function MobileMenu({ open, onClose, isActive }) {
    const { t, site } = useSite();

    return (
        <AnimatePresence>
            {open && (
                <motion.div
                    initial={{ clipPath: 'inset(0% 0% 100% 0%)' }}
                    animate={{ clipPath: 'inset(0% 0% 0% 0%)' }}
                    exit={{ clipPath: 'inset(0% 0% 100% 0%)' }}
                    transition={{ duration: 0.75, ease: EASE.inOutQuint }}
                    className="noise-overlay fixed inset-0 z-55 overflow-y-auto bg-ink-950 text-concrete-100"
                >
                    <div className="blueprint-grid absolute inset-0 opacity-60" />

                    <div className="shell relative flex min-h-full flex-col justify-between py-28">
                        <nav aria-label="Navigation mobile">
                            <span className="eyebrow text-concrete-500">{t('nav.all_pages')}</span>

                            <ul className="mt-8 space-y-1">
                                {[{ key: 'home', href: '/' }, ...NAV].map((item, index) => (
                                    <motion.li
                                        key={item.key}
                                        initial={{ opacity: 0, y: 26 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        transition={{
                                            duration: 0.7,
                                            delay: 0.22 + index * 0.045,
                                            ease: EASE.outExpo,
                                        }}
                                    >
                                        <Link
                                            href={item.href}
                                            onClick={onClose}
                                            className={cn(
                                                'flex items-baseline gap-5 py-2 font-display text-[clamp(2rem,9vw,3.75rem)] font-black tracking-[-0.04em] transition-colors duration-400',
                                                isActive(item.href)
                                                    ? 'text-brand'
                                                    : 'text-concrete-100 hover:text-brand',
                                            )}
                                        >
                                            <span className="font-mono text-[0.625rem] tracking-[0.2em] text-concrete-600">
                                                {String(index + 1).padStart(2, '0')}
                                            </span>
                                            {t(`nav.${item.key}`)}
                                        </Link>
                                    </motion.li>
                                ))}
                            </ul>
                        </nav>

                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.7, delay: 0.6, ease: EASE.outExpo }}
                            className="mt-16 border-t border-ink-700 pt-10"
                        >
                            <Button href="/devis" tone="brand" size="lg" className="w-full sm:w-auto">
                                {t('nav.quote')}
                            </Button>

                            <div className="mt-10 grid gap-6 font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-concrete-400 sm:grid-cols-2">
                                <div>
                                    <span className="block text-concrete-600">{t('common.call_us')}</span>
                                    <a
                                        href={`tel:${site.contact?.phone?.replace(/\s/g, '')}`}
                                        className="mt-1 block text-concrete-100"
                                    >
                                        {site.contact?.phone}
                                    </a>
                                </div>
                                <div>
                                    <span className="block text-concrete-600">{t('common.write_us')}</span>
                                    <a
                                        href={`mailto:${site.contact?.email}`}
                                        className="mt-1 block break-all text-concrete-100"
                                    >
                                        {site.contact?.email}
                                    </a>
                                </div>
                            </div>

                            {/* The bar's own switcher is hidden on a phone, so
                                this is the only one a small screen ever sees —
                                it takes the same component rather than a second
                                copy that has to be kept in step with it. */}
                            <LocaleSwitcher tone="light" className="mt-8" />
                        </motion.div>
                    </div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}
