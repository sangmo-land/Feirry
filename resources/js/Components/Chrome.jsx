import { useEffect, useMemo, useRef, useState } from 'react';
import { router } from '@inertiajs/react';
import { AnimatePresence, motion, useScroll, useSpring } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { useMediaQuery, useReducedMotion, useSite } from '@/Hooks';
import { scrollToTop } from '@/Hooks/useSmoothScroll';
import { Icon } from '@/Components/Ui';
import { LogoWordmark } from '@/Components/Logo';

/* ------------------------------------------------------------------ */
/* ScrollProgress: hairline at the very top of the viewport.           */
/* ------------------------------------------------------------------ */
export function ScrollProgress() {
    const { scrollYProgress } = useScroll();
    const scaleX = useSpring(scrollYProgress, { stiffness: 220, damping: 40, restDelta: 0.001 });

    return (
        <motion.div
            style={{ scaleX }}
            className="fixed inset-x-0 top-0 z-60 h-0.5 origin-left bg-brand"
            aria-hidden="true"
        />
    );
}

/* ------------------------------------------------------------------ */
/* Cursor: a small dot with a trailing ring that grows over links.     */
/* Pointer-fine devices only; touch users never see it.                */
/* ------------------------------------------------------------------ */
export function Cursor() {
    const isFine = useMediaQuery('(hover: hover) and (pointer: fine)');
    const dotRef = useRef(null);
    const ringRef = useRef(null);
    const [variant, setVariant] = useState('default');

    useEffect(() => {
        if (!isFine) return undefined;

        const dot = dotRef.current;
        const ring = ringRef.current;
        if (!dot || !ring) return undefined;

        const pointer = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
        const trail = { ...pointer };
        let frame;

        const move = (event) => {
            pointer.x = event.clientX;
            pointer.y = event.clientY;
            dot.style.transform = `translate3d(${pointer.x}px, ${pointer.y}px, 0) translate(-50%, -50%)`;
        };

        const loop = () => {
            trail.x += (pointer.x - trail.x) * 0.14;
            trail.y += (pointer.y - trail.y) * 0.14;
            ring.style.transform = `translate3d(${trail.x}px, ${trail.y}px, 0) translate(-50%, -50%)`;
            frame = requestAnimationFrame(loop);
        };

        const over = (event) => {
            const target = event.target.closest('a, button, [role="button"], input, textarea, select');
            setVariant(target ? 'active' : 'default');
        };

        window.addEventListener('pointermove', move, { passive: true });
        window.addEventListener('pointerover', over, { passive: true });
        frame = requestAnimationFrame(loop);

        return () => {
            cancelAnimationFrame(frame);
            window.removeEventListener('pointermove', move);
            window.removeEventListener('pointerover', over);
        };
    }, [isFine]);

    if (!isFine) return null;

    return (
        <div className="pointer-events-none fixed inset-0 z-90 hidden xl:block" aria-hidden="true">
            <div
                ref={dotRef}
                className={cn(
                    'fixed left-0 top-0 size-1.5 rounded-full bg-brand transition-opacity duration-300',
                    variant === 'active' && 'opacity-0',
                )}
            />
            <div
                ref={ringRef}
                className={cn(
                    'fixed left-0 top-0 rounded-full border border-ink-900/40 transition-[width,height,background-color,border-color] duration-400 [transition-timing-function:var(--ease-out-expo)]',
                    variant === 'active'
                        ? 'size-12 border-brand bg-brand/15'
                        : 'size-8 bg-transparent',
                )}
            />
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* Preloader: counts to 100 on first paint, then wipes away.           */
/* Shown once per session so repeat navigation stays fast.             */
/* ------------------------------------------------------------------ */
export function Preloader() {
    const { site } = useSite();
    const [visible, setVisible] = useState(() => {
        if (typeof window === 'undefined') return false;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return false;
        try {
            return !window.sessionStorage.getItem('feirry:seen');
        } catch {
            return true;
        }
    });
    const [progress, setProgress] = useState(0);

    useEffect(() => {
        if (!visible) return undefined;

        if (window.__lenis) window.__lenis.stop();
        document.body.style.overflow = 'hidden';

        const started = performance.now();
        const duration = 1500;
        let frame;

        const tick = (now) => {
            const ratio = Math.min((now - started) / duration, 1);
            setProgress(Math.round((1 - Math.pow(1 - ratio, 3)) * 100));
            if (ratio < 1) {
                frame = requestAnimationFrame(tick);
            } else {
                window.setTimeout(() => setVisible(false), 260);
            }
        };

        frame = requestAnimationFrame(tick);

        return () => cancelAnimationFrame(frame);
    }, [visible]);

    useEffect(() => {
        if (visible) return;

        document.body.style.overflow = '';
        if (window.__lenis) window.__lenis.start();
        try {
            window.sessionStorage.setItem('feirry:seen', '1');
        } catch {
            /* private mode: just show it again next time */
        }
    }, [visible]);

    return (
        <AnimatePresence>
            {visible && (
                <motion.div
                    key="preloader"
                    exit={{ clipPath: 'inset(0% 0% 100% 0%)' }}
                    transition={{ duration: 0.95, ease: EASE.inOutQuint }}
                    className="noise-overlay fixed inset-0 z-100 flex flex-col justify-between bg-ink-950 p-6 sm:p-10"
                >
                    <div className="blueprint-grid absolute inset-0 opacity-60" />

                    <motion.div
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.7, ease: EASE.outExpo }}
                        className="relative"
                    >
                        <LogoWordmark className="h-5 w-auto text-brand" />
                        <span className="mt-2 block font-mono text-[0.5625rem] uppercase tracking-[0.24em] text-concrete-600">
                            {site.contact?.city} · {site.contact?.country}
                        </span>
                    </motion.div>

                    <div className="relative">
                        <div className="flex items-end justify-between gap-6">
                            <span className="font-mono text-[0.625rem] uppercase tracking-[0.2em] text-concrete-600">
                                {site.brand?.tagline}
                            </span>
                            <span className="font-display text-[clamp(3.5rem,13vw,9rem)] font-black leading-[0.8] tracking-[-0.05em] tabular-nums text-concrete-50">
                                {String(progress).padStart(3, '0')}
                            </span>
                        </div>

                        <div className="mt-6 h-px w-full bg-ink-700">
                            <div
                                className="h-full bg-brand transition-[width] duration-100 ease-linear"
                                style={{ width: `${progress}%` }}
                            />
                        </div>
                    </div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}

/* ------------------------------------------------------------------ */
/* PageTransition: the veil between pages.                             */
/*                                                                     */
/* This was an opaque curtain, which answered the wrong question. A    */
/* reader waiting on a page cannot tell a slow one from a broken one   */
/* when there is nothing behind the black to look at and nothing on it */
/* to read. So the panel is a scrim now: the page being left stays     */
/* legible underneath, dimmed and set back, and the wait says what it  */
/* is — a hazard bar running across the top and a mono label, both of  */
/* which keep moving for as long as the visit takes.                   */
/*                                                                     */
/* Neither is a measurement. Inertia cannot know how far along a visit */
/* is, so nothing here pretends to; what they report is that work is   */
/* still going on, which is the honest half of the answer.             */
/*                                                                     */
/* Nothing shows for the first breath. Most visits land inside it, and */
/* a panel that flashes up and straight back out reads as a fault      */
/* rather than as feedback — so the scrim is armed on a timer that a   */
/* quick visit cancels before it ever fires.                           */
/* ------------------------------------------------------------------ */

/* Long enough to swallow a visit served from cache, short enough that a
   reader on a slow connection is not left wondering whether the click
   registered at all. */
const VEIL_DELAY = 180;

/*
 * Only navigation gets the veil. The filters on the projects and journal
 * indexes visit with `preserveState` — those redraw a list in place rather
 * than replacing the page, and covering the whole screen to swap six cards is
 * the wrong weight of gesture. Inertia's own progress bar carries them.
 */
const replacesPage = (visit) =>
    visit?.method === 'get' && !visit?.preserveState && !visit?.only?.length;

export function PageTransition() {
    const { t } = useSite();
    const [active, setActive] = useState(false);

    useEffect(() => {
        // Counted rather than flagged. Clicking a second link before the first
        // lands cancels the first, and Inertia finishes the cancelled visit
        // after the new one has already started — a single flag would let that
        // late finish put the veil away while the page is still on its way.
        let pending = 0;
        let armTimer;
        let hideTimer;

        const offStart = router.on('start', (event) => {
            if (!replacesPage(event.detail?.visit)) return;

            pending += 1;
            window.clearTimeout(hideTimer);

            if (pending === 1) {
                armTimer = window.setTimeout(() => setActive(true), VEIL_DELAY);
            }
        });

        const offFinish = router.on('finish', (event) => {
            const visit = event.detail?.visit;

            // Inertia's own contract: a visit resets the scroll unless it asked
            // to keep it. This used to fire on every finish, which threw the
            // reader back to the top of the projects index each time they
            // touched a filter chip — the one visit that asks to stay put.
            if (!visit?.preserveScroll) scrollToTop();

            if (!replacesPage(visit)) return;

            pending = Math.max(0, pending - 1);
            if (pending > 0) return;

            // Clears the arming timer too, which is what keeps a fast visit
            // from ever painting the scrim.
            window.clearTimeout(armTimer);
            hideTimer = window.setTimeout(() => setActive(false), 180);
        });

        return () => {
            window.clearTimeout(armTimer);
            window.clearTimeout(hideTimer);
            offStart();
            offFinish();
        };
    }, []);

    return (
        <AnimatePresence>
            {active && (
                <motion.div
                    key="veil"
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.4, ease: EASE.outExpo }}
                    className="pointer-events-none fixed inset-0 z-80 flex items-center justify-center bg-ink-950/70 backdrop-blur-[3px]"
                >
                    {/* The stripes travel exactly one period of the pattern, so
                        the loop has no seam in it. */}
                    <div className="absolute inset-x-0 top-0 h-1.5 overflow-hidden">
                        <div className="hazard-stripes animate-hazard-run absolute inset-y-0 -left-[28.284px] w-[calc(100%+28.284px)]" />
                    </div>

                    <motion.div
                        role="status"
                        initial={{ opacity: 0, y: 10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: 6 }}
                        transition={{ duration: 0.45, ease: EASE.outExpo }}
                        className="flex items-center gap-3 rounded-full border border-ink-700/70 bg-ink-950/85 px-5 py-3 shadow-lift"
                    >
                        <Icon
                            name="loader-circle"
                            className="size-4 animate-spin text-brand"
                            strokeWidth={2}
                        />
                        <span className="font-mono text-[0.625rem] uppercase tracking-[0.2em] text-concrete-300">
                            {t('common.loading')}
                        </span>
                    </motion.div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}

/* ------------------------------------------------------------------ */
/* FloatingActions: the social rail + back to top.                     */
/*                                                                     */
/* The corner used to hold a WhatsApp button on its own. It now holds  */
/* one trigger that unrolls the whole set upward, WhatsApp included —  */
/* the networks are worth reaching, but not five permanent circles     */
/* stacked down the side of every page.                                */
/*                                                                     */
/* Which networks appear is the client's to decide: the four links are */
/* editable in the dashboard, and an emptied one drops out of the rail */
/* and the footer alike. WhatsApp rides on the number in Coordonnées.  */
/* ------------------------------------------------------------------ */

/*
 * Rail order. WhatsApp sits closest to the trigger: it is the one people
 * actually reach for here, and the shortest travel should belong to it.
 * `flex-col-reverse` puts it at the bottom of the column while leaving it
 * first in the DOM, so it also takes focus first.
 */
const SOCIAL_RAIL = ['whatsapp', 'facebook', 'linkedin', 'instagram', 'tiktok', 'youtube'];

const SOCIAL_LABELS = {
    whatsapp: 'WhatsApp',
    facebook: 'Facebook',
    linkedin: 'LinkedIn',
    instagram: 'Instagram',
    tiktok: 'TikTok',
    youtube: 'YouTube',
};

/*
 * Each network in its own colour.
 *
 * These are the one set of values on the site that are not ours to choose:
 * they belong to the platforms, and the colour is most of what makes a mark
 * recognisable at eleven pixels. So they sit here as raw hexadecimals rather
 * than in the token sheet, which describes FEIRRY and should not be made to
 * carry Meta's blue.
 *
 * Instagram has no flat colour — the gradient is the identity — so it gets the
 * corner-to-corner wash the brand actually uses. TikTok is black on white or
 * white on black and nothing else; it takes the site's own near-black, which
 * is the one place a token and a brand colour happen to agree.
 */
const SOCIAL_SKIN = {
    whatsapp: 'bg-jade-bright',
    facebook: 'bg-[#1877F2]',
    linkedin: 'bg-[#0A66C2]',
    instagram:
        'bg-[radial-gradient(circle_at_30%_107%,#FDF497_0%,#FD5949_45%,#D6249F_60%,#285AEB_90%)]',
    tiktok: 'bg-ink-950',
    youtube: 'bg-[#FF0000]',
};

const RAIL_ITEM = {
    hidden: { opacity: 0, y: 16, scale: 0.6 },
    visible: { opacity: 1, y: 0, scale: 1, transition: { duration: 0.5, ease: EASE.outExpo } },
};

export function FloatingActions() {
    const { site, t } = useSite();
    const reduced = useReducedMotion();
    const [showTop, setShowTop] = useState(false);
    const [open, setOpen] = useState(false);
    const corner = useRef(null);

    const links = useMemo(() => {
        const social = site.social ?? {};
        const whatsapp = site.contact?.whatsapp;

        return SOCIAL_RAIL.map((key) => {
            const href = key === 'whatsapp'
                ? (whatsapp ? `https://wa.me/${whatsapp}` : null)
                : social[key];

            return href ? { key, href } : null;
        }).filter(Boolean);
    }, [site.social, site.contact?.whatsapp]);

    useEffect(() => {
        const onScroll = () => setShowTop(window.scrollY > 900);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    // An open rail closes on Escape, or on a press anywhere else. Both are
    // expected of a thing that opens over the page, and neither costs the
    // reader a second trip to the button.
    useEffect(() => {
        if (!open) return undefined;

        const onKey = (event) => {
            if (event.key === 'Escape') setOpen(false);
        };
        const onPress = (event) => {
            if (!corner.current?.contains(event.target)) setOpen(false);
        };

        window.addEventListener('keydown', onKey);
        window.addEventListener('pointerdown', onPress);

        return () => {
            window.removeEventListener('keydown', onKey);
            window.removeEventListener('pointerdown', onPress);
        };
    }, [open]);

    return (
        <div
            ref={corner}
            className="fixed bottom-5 right-5 z-45 flex flex-col items-end gap-3 sm:bottom-8 sm:right-8"
        >
            <AnimatePresence>
                {showTop && (
                    <motion.button
                        key="top"
                        type="button"
                        // Laid out above the rail, so it slides rather than
                        // jumping when the rail pushes it up.
                        layout={!reduced}
                        onClick={() => window.__lenis?.scrollTo(0, { duration: 1.4 }) ?? window.scrollTo({ top: 0, behavior: 'smooth' })}
                        initial={{ opacity: 0, scale: 0.7, y: 12 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.7, y: 12 }}
                        transition={{ duration: 0.4, ease: EASE.outExpo }}
                        aria-label="Retour en haut"
                        className="flex size-12 items-center justify-center rounded-full border border-ink-900/15 bg-concrete-50/90 text-ink-900 shadow-[var(--shadow-panel)] backdrop-blur transition-colors duration-400 hover:bg-ink-900 hover:text-concrete-50"
                    >
                        <Icon name="arrow-up" className="size-4" strokeWidth={2} />
                    </motion.button>
                )}
            </AnimatePresence>

            {links.length > 0 && (
                <div className="flex flex-col items-end gap-3">
                    <AnimatePresence>
                        {open && (
                            <motion.ul
                                key="rail"
                                id="social-rail"
                                initial="hidden"
                                animate="visible"
                                exit="hidden"
                                variants={{
                                    // Out in the order they were listed, back in
                                    // the reverse: the rail rolls up from the
                                    // button and rolls back down into it.
                                    visible: { transition: { staggerChildren: 0.055, delayChildren: 0.02 } },
                                    hidden: { transition: { staggerChildren: 0.04, staggerDirection: -1 } },
                                }}
                                className="flex flex-col-reverse items-end gap-3"
                            >
                                {links.map(({ key, href }) => (
                                    <motion.li key={key} variants={RAIL_ITEM}>
                                        <a
                                            href={href}
                                            target="_blank"
                                            rel="noreferrer"
                                            aria-label={SOCIAL_LABELS[key]}
                                            className={cn(
                                                'flex size-11 items-center justify-center rounded-full text-white shadow-[var(--shadow-panel)]',
                                                // Brightness rather than a colour
                                                // change on hover: the point of
                                                // the circle is the colour, so
                                                // there is nothing to swap it for.
                                                'transition-[filter,transform] duration-400 [transition-timing-function:var(--ease-out-expo)] hover:scale-110 hover:brightness-110',
                                                // The rail crosses light sections
                                                // and the near-black footer alike.
                                                // A pale hairline is what keeps
                                                // TikTok's black circle from
                                                // dissolving into the latter.
                                                'ring-1 ring-white/25',
                                                SOCIAL_SKIN[key],
                                            )}
                                        >
                                            <Icon name={key} className="size-5" />
                                        </a>
                                    </motion.li>
                                ))}
                            </motion.ul>
                        )}
                    </AnimatePresence>

                    <button
                        type="button"
                        onClick={() => setOpen((value) => !value)}
                        aria-expanded={open}
                        aria-controls="social-rail"
                        aria-label={open ? t('common.socials_close') : t('common.socials')}
                        className="relative flex size-13 items-center justify-center rounded-full bg-brand text-ink-950 shadow-[0_10px_30px_-8px_rgb(254_80_0/0.55)] transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] hover:scale-105"
                    >
                        {/* The ring is an invitation, so it stops once the
                            invitation has been taken up. */}
                        {!open && (
                            <span className="absolute inset-0 animate-pulse-ring rounded-full bg-brand/40" />
                        )}

                        <span className="relative flex size-6 items-center justify-center">
                            <Icon
                                name="share-2"
                                strokeWidth={2}
                                className={cn(
                                    'absolute size-5 transition-all duration-400 [transition-timing-function:var(--ease-out-expo)]',
                                    open ? 'rotate-90 scale-50 opacity-0' : 'rotate-0 scale-100 opacity-100',
                                )}
                            />
                            <Icon
                                name="x"
                                strokeWidth={2}
                                className={cn(
                                    'absolute size-5 transition-all duration-400 [transition-timing-function:var(--ease-out-expo)]',
                                    open ? 'rotate-0 scale-100 opacity-100' : '-rotate-90 scale-50 opacity-0',
                                )}
                            />
                        </span>
                    </button>
                </div>
            )}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* FlashToast: server flash messages surfaced as a toast.              */
/* ------------------------------------------------------------------ */
export function FlashToast() {
    const { flash } = useSite();
    const [message, setMessage] = useState(null);

    useEffect(() => {
        const next = flash?.success ?? flash?.error ?? null;
        if (!next) return undefined;

        setMessage({ text: next, tone: flash?.success ? 'success' : 'error' });
        const timer = window.setTimeout(() => setMessage(null), 8000);

        return () => window.clearTimeout(timer);
    }, [flash?.success, flash?.error]);

    return (
        <AnimatePresence>
            {message && (
                <motion.div
                    key="toast"
                    initial={{ opacity: 0, y: 40, scale: 0.97 }}
                    animate={{ opacity: 1, y: 0, scale: 1 }}
                    exit={{ opacity: 0, y: 20, scale: 0.97 }}
                    transition={{ duration: 0.5, ease: EASE.outExpo }}
                    role="status"
                    className="fixed bottom-6 left-1/2 z-70 w-[min(30rem,calc(100vw-2.5rem))] -translate-x-1/2"
                >
                    <div
                        className={cn(
                            'flex items-start gap-4 border-l-4 bg-ink-900 p-5 shadow-[var(--shadow-lift)]',
                            message.tone === 'success' ? 'border-jade-bright' : 'border-clay',
                        )}
                    >
                        <Icon
                            name={message.tone === 'success' ? 'circle-check' : 'circle-alert'}
                            className={cn(
                                'mt-0.5 size-5 shrink-0',
                                message.tone === 'success' ? 'text-jade-bright' : 'text-clay',
                            )}
                        />
                        <p className="flex-1 text-sm leading-relaxed text-concrete-100">{message.text}</p>
                        <button
                            type="button"
                            onClick={() => setMessage(null)}
                            aria-label="Fermer"
                            className="text-concrete-500 transition-colors hover:text-concrete-100"
                        >
                            <Icon name="x" className="size-4" />
                        </button>
                    </div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}
