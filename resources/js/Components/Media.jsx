import { useCallback, useEffect, useRef, useState } from 'react';
import { createPortal } from 'react-dom';
import { AnimatePresence, motion, useScroll, useSpring, useTransform } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { useReducedMotion, useScrollLock, useSite } from '@/Hooks';
import { Icon } from '@/Components/Ui';

/* ------------------------------------------------------------------ */
/* Lightbox                                                            */
/*                                                                     */
/* One overlay serves every enlargeable image on the site: the project */
/* and service galleries, the full-bleed covers, the split-feature     */
/* photographs. Hero plates are deliberately left out — the hero is    */
/* the page's opening statement, not something to be inspected — and   */
/* so is any image sitting inside a link, where a click has to take    */
/* the visitor to the page rather than stop to enlarge a thumbnail.    */
/*                                                                     */
/* A project gallery is the one place on this site where a visitor is  */
/* studying the work rather than reading about it, so the viewer is    */
/* built for that: step through with keys or a swipe, jump about on    */
/* the filmstrip, and zoom in far enough to look at how a junction was */
/* actually finished. Everything that only makes sense for a set —     */
/* arrows, counter, filmstrip — hides itself for a single image.       */
/* ------------------------------------------------------------------ */
const ZOOM_MAX = 4;
const ZOOM_STEP = 0.5;
/* Past this a horizontal drag is a page turn rather than a wobble. */
const SWIPE_THRESHOLD = 70;

export function Lightbox({ images = [], index = 0, alt = '', onClose, onIndex }) {
    const { t } = useSite();
    const reduced = useReducedMotion();
    const many = images.length > 1;

    const [zoom, setZoom] = useState(1);
    const [direction, setDirection] = useState(0);
    const [loaded, setLoaded] = useState(false);

    const dialogRef = useRef(null);
    const stageRef = useRef(null);
    const closeRef = useRef(null);
    const restoreRef = useRef(null);
    const activeThumbRef = useRef(null);
    const pressRef = useRef(null);

    useScrollLock(true);

    const zoomed = zoom > 1;

    const show = useCallback(
        (next, dir = 0) => {
            setDirection(dir);
            setZoom(1);
            onIndex?.(next);
        },
        [onIndex],
    );

    const step = useCallback(
        (delta) => {
            if (!many) return;
            show((index + delta + images.length) % images.length, delta);
        },
        [images.length, index, many, show],
    );

    const nudgeZoom = useCallback(
        (delta) => setZoom((current) => Math.min(ZOOM_MAX, Math.max(1, current + delta))),
        [],
    );

    /* Neighbours are fetched while the current frame is on screen, so stepping
       through a set does not blink through an empty stage each time. */
    useEffect(() => {
        if (!many) return;

        [1, -1].forEach((delta) => {
            const preload = new Image();
            preload.src = images[(index + delta + images.length) % images.length];
        });
    }, [images, index, many]);

    useEffect(() => setLoaded(false), [index]);

    useEffect(() => {
        activeThumbRef.current?.scrollIntoView({
            behavior: reduced ? 'auto' : 'smooth',
            inline: 'center',
            block: 'nearest',
        });
    }, [index, reduced]);

    /* The wheel has to be bound by hand: React registers its own listener as
       passive, and a passive listener cannot stop the gesture reaching the
       page underneath. */
    useEffect(() => {
        const stage = stageRef.current;
        if (!stage) return undefined;

        const onWheel = (event) => {
            event.preventDefault();
            nudgeZoom(event.deltaY > 0 ? -ZOOM_STEP / 2 : ZOOM_STEP / 2);
        };

        stage.addEventListener('wheel', onWheel, { passive: false });

        return () => stage.removeEventListener('wheel', onWheel);
    }, [nudgeZoom]);

    useEffect(() => {
        restoreRef.current = document.activeElement;
        closeRef.current?.focus();

        const onKey = (event) => {
            switch (event.key) {
                case 'Escape':
                    onClose?.();
                    break;
                case 'ArrowLeft':
                    step(-1);
                    break;
                case 'ArrowRight':
                    step(1);
                    break;
                case 'Home':
                    show(0, -1);
                    break;
                case 'End':
                    show(images.length - 1, 1);
                    break;
                case '+':
                case '=':
                    nudgeZoom(ZOOM_STEP);
                    break;
                case '-':
                    nudgeZoom(-ZOOM_STEP);
                    break;
                case '0':
                    setZoom(1);
                    break;
                case 'Tab': {
                    /* Tab cycles inside the dialog rather than wandering off
                       into the page still sitting behind it. */
                    const stops = dialogRef.current?.querySelectorAll('button');
                    if (!stops?.length) return;

                    const first = stops[0];
                    const last = stops[stops.length - 1];

                    if (event.shiftKey && document.activeElement === first) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        event.preventDefault();
                        first.focus();
                    }
                    return;
                }
                default:
                    return;
            }

            event.preventDefault();
        };

        window.addEventListener('keydown', onKey);

        return () => {
            window.removeEventListener('keydown', onKey);
            restoreRef.current?.focus?.();
        };
    }, [images.length, nudgeZoom, onClose, show, step]);

    const control =
        'flex size-11 shrink-0 items-center justify-center rounded-full border border-concrete-50/20 text-concrete-50 backdrop-blur transition-colors duration-300 hover:bg-concrete-50 hover:text-ink-950 disabled:pointer-events-none disabled:opacity-30';

    /* Rendered into <body>: a `fixed` overlay is trapped by any ancestor that
       carries a transform or a clip, and these frames are full of both — the
       parallax wrapper clips, ClipReveal wipes with clip-path, and half the
       page sits inside a motion element mid-reveal. */
    return createPortal(
        <motion.div
            ref={dialogRef}
            role="dialog"
            aria-modal="true"
            aria-label={alt || t('projects.gallery')}
            className="fixed inset-0 z-100 flex flex-col bg-ink-950/92 backdrop-blur-md"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.4, ease: EASE.outExpo }}
            onClick={onClose}
        >
            {/* Top bar */}
            <div
                className="relative z-20 flex shrink-0 items-center justify-between gap-4 px-4 py-4 sm:px-6"
                onClick={(event) => event.stopPropagation()}
            >
                <div className="min-w-0">
                    {many && (
                        <span className="font-mono text-xs tracking-[0.2em] text-concrete-400">
                            {String(index + 1).padStart(2, '0')} / {String(images.length).padStart(2, '0')}
                        </span>
                    )}
                    {alt && (
                        <p className="truncate font-mono text-[0.625rem] uppercase tracking-[0.16em] text-concrete-600">
                            {alt}
                        </p>
                    )}
                </div>

                <div className="flex items-center gap-2">
                    <button
                        type="button"
                        onClick={() => nudgeZoom(-ZOOM_STEP)}
                        disabled={zoom <= 1}
                        className={control}
                        aria-label={t('common.zoom_out')}
                    >
                        <Icon name="zoom-out" className="size-4" />
                    </button>

                    <span className="w-12 text-center font-mono text-[0.625rem] tracking-[0.14em] text-concrete-500">
                        {Math.round(zoom * 100)}%
                    </span>

                    <button
                        type="button"
                        onClick={() => nudgeZoom(ZOOM_STEP)}
                        disabled={zoom >= ZOOM_MAX}
                        className={control}
                        aria-label={t('common.zoom_in')}
                    >
                        <Icon name="zoom-in" className="size-4" />
                    </button>

                    <button
                        ref={closeRef}
                        type="button"
                        onClick={onClose}
                        className={cn(control, 'ml-2')}
                        aria-label={t('common.close')}
                    >
                        <Icon name="x" className="size-5" />
                    </button>
                </div>
            </div>

            {/* Stage */}
            <div ref={stageRef} className="relative min-h-0 flex-1">
                {many && (
                    <button
                        type="button"
                        onClick={(event) => {
                            event.stopPropagation();
                            step(-1);
                        }}
                        className={cn(control, 'absolute left-3 top-1/2 z-20 -translate-y-1/2 sm:left-6')}
                        aria-label={t('common.previous')}
                    >
                        <Icon name="arrow-left" className="size-5" />
                    </button>
                )}

                {!loaded && (
                    <span className="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-concrete-600">
                        <Icon name="loader-circle" className="size-7 animate-spin" />
                    </span>
                )}

                {/* The slide-in lives on the frame, not on the picture. Both
                    want the x transform — the frame to enter with, the picture
                    to be dragged by — and sharing it left the image parked at
                    its entry offset instead of centred. */}
                <motion.div
                    key={images[index]}
                    className="absolute inset-0 flex items-center justify-center overflow-hidden p-4 sm:p-8"
                    initial={reduced ? false : { opacity: 0, x: direction * 60 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ duration: 0.5, ease: EASE.outExpo }}
                >
                    <motion.img
                        src={images[index]}
                        alt={alt}
                        onLoad={() => setLoaded(true)}
                        draggable={false}
                        /* Zoomed, a drag pans the picture; at rest it turns the
                           page, which is what a thumb expects on a phone. */
                        drag={zoomed ? true : many && 'x'}
                        dragConstraints={zoomed ? stageRef : { left: 0, right: 0 }}
                        dragElastic={zoomed ? 0 : 0.16}
                        dragMomentum={false}
                        onDragEnd={(event, info) => {
                            if (zoomed) return;
                            if (info.offset.x < -SWIPE_THRESHOLD) step(1);
                            else if (info.offset.x > SWIPE_THRESHOLD) step(-1);
                        }}
                        onPointerDown={(event) => {
                            pressRef.current = { x: event.clientX, y: event.clientY };
                        }}
                        onClick={(event) => {
                            event.stopPropagation();

                            /* A drag ends in a click too, so only a press that
                               stayed put counts as a tap to zoom. */
                            const start = pressRef.current;
                            const moved =
                                start &&
                                Math.hypot(event.clientX - start.x, event.clientY - start.y) > 6;

                            if (!moved) setZoom((current) => (current > 1 ? 1 : 2));
                        }}
                        /* While zoomed the drag owns x and y, so they are left
                           out of the target. Coming back to 100% pins them,
                           or the picture stays wherever it was panned to. */
                        animate={{
                            scale: zoom,
                            opacity: loaded ? 1 : 0,
                            ...(zoomed ? {} : { x: 0, y: 0 }),
                        }}
                        transition={{ duration: 0.5, ease: EASE.outExpo }}
                        className={cn(
                            'max-h-full max-w-full touch-none select-none object-contain shadow-[var(--shadow-lift)]',
                            zoomed ? 'cursor-grab active:cursor-grabbing' : 'cursor-zoom-in',
                        )}
                    />
                </motion.div>

                {many && (
                    <button
                        type="button"
                        onClick={(event) => {
                            event.stopPropagation();
                            step(1);
                        }}
                        className={cn(control, 'absolute right-3 top-1/2 z-20 -translate-y-1/2 sm:right-6')}
                        aria-label={t('common.next')}
                    >
                        <Icon name="arrow-right" className="size-5" />
                    </button>
                )}
            </div>

            {/* Filmstrip */}
            {many && (
                <div
                    className="relative z-20 shrink-0 px-4 pb-5 pt-3 sm:px-6"
                    onClick={(event) => event.stopPropagation()}
                >
                    <div className="mask-fade-x flex justify-start gap-2 overflow-x-auto pb-1 sm:justify-center">
                        {images.map((image, position) => {
                            const current = position === index;

                            return (
                                <button
                                    key={image}
                                    ref={current ? activeThumbRef : null}
                                    type="button"
                                    onClick={() => show(position, position > index ? 1 : -1)}
                                    aria-label={`${position + 1}`}
                                    aria-current={current}
                                    className={cn(
                                        'relative h-12 w-20 shrink-0 overflow-hidden bg-ink-900 transition-[opacity,outline-color] duration-300',
                                        'outline outline-2 outline-offset-2',
                                        current
                                            ? 'opacity-100 outline-brand'
                                            : 'opacity-40 outline-transparent hover:opacity-80',
                                    )}
                                >
                                    <img
                                        src={image}
                                        alt=""
                                        loading="lazy"
                                        className="h-full w-full object-cover"
                                    />
                                </button>
                            );
                        })}
                    </div>
                </div>
            )}
        </motion.div>,
        document.body,
    );
}

/* ------------------------------------------------------------------ */
/* ZoomTrigger: the affordance laid over an enlargeable image.         */
/*                                                                     */
/* Absolutely positioned, so it drops into an existing frame without   */
/* disturbing the layout, and a real <button>, so the image is         */
/* reachable by keyboard rather than click-only. The badge rises on    */
/* focus as well as on hover, or it would be invisible to anyone       */
/* tabbing through.                                                    */
/* ------------------------------------------------------------------ */
export function ZoomTrigger({ onClick, label }) {
    return (
        <button
            type="button"
            onClick={onClick}
            aria-label={label}
            className="group/zoom absolute inset-0 z-10 cursor-zoom-in"
        >
            <span className="absolute inset-0 bg-ink-950/0 transition-colors duration-500 group-hover/zoom:bg-ink-950/20" />
            {/* Top right, not bottom: the split feature hangs its stat panel
                over the lower corner and the badge would sit under it. */}
            <span className="absolute right-4 top-4 flex size-10 -translate-y-2 items-center justify-center rounded-full bg-concrete-50/90 text-ink-900 opacity-0 shadow-[var(--shadow-panel)] backdrop-blur transition-[opacity,transform] duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/zoom:translate-y-0 group-hover/zoom:opacity-100 group-focus-visible/zoom:translate-y-0 group-focus-visible/zoom:opacity-100">
                <Icon name="maximize-2" className="size-4" />
            </span>
        </button>
    );
}

/**
 * The plumbing behind a single enlargeable image: the open state, the trigger
 * and the overlay. Callers render their own frame, so the parallax cover, the
 * split feature and anything added later all behave identically without
 * repeating it.
 */
export function useZoom(src, alt = '') {
    const [open, setOpen] = useState(false);
    const { t } = useSite();

    return {
        trigger: src ? <ZoomTrigger onClick={() => setOpen(true)} label={t('common.enlarge')} /> : null,
        overlay: (
            <AnimatePresence>
                {open && <Lightbox images={[src]} alt={alt} onClose={() => setOpen(false)} />}
            </AnimatePresence>
        ),
    };
}

/* ------------------------------------------------------------------ */
/* ParallaxImage: the image drifts slower than the page as it passes.  */
/* The wrapper clips, the image is oversized, so no edges show.        */
/* ------------------------------------------------------------------ */
export function ParallaxImage({
    src,
    alt = '',
    className,
    imgClassName,
    strength = 18,
    scale = 1.18,
    priority = false,
    overlay = false,
    zoomable = false,
}) {
    const ref = useRef(null);
    const reduced = useReducedMotion();
    const zoom = useZoom(src, alt);

    const { scrollYProgress } = useScroll({
        target: ref,
        offset: ['start end', 'end start'],
    });

    const y = useTransform(scrollYProgress, [0, 1], [`-${strength}%`, `${strength}%`]);
    const smoothY = useSpring(y, { stiffness: 120, damping: 30, mass: 0.4 });

    return (
        <div ref={ref} className={cn('relative overflow-hidden bg-ink-850', className)}>
            <motion.img
                src={src}
                alt={alt}
                loading={priority ? 'eager' : 'lazy'}
                fetchPriority={priority ? 'high' : 'auto'}
                decoding="async"
                style={reduced ? undefined : { y: smoothY, scale }}
                className={cn('h-full w-full object-cover', imgClassName)}
            />
            {overlay && (
                <div className="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink-950/80 via-ink-950/10 to-transparent" />
            )}
            {zoomable && zoom.trigger}
            {zoomable && zoom.overlay}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* HoverImage: scales on hover with a slow expo curve. Used on cards.  */
/* No zoom here: every one of these sits inside a link, where a click  */
/* belongs to the card.                                                */
/* ------------------------------------------------------------------ */
export function HoverImage({ src, alt = '', className, imgClassName, ratio = 'aspect-[4/3]', children }) {
    const [loaded, setLoaded] = useState(false);

    return (
        <div className={cn('group/img relative overflow-hidden bg-ink-850', ratio, className)}>
            {src ? (
                <img
                    src={src}
                    alt={alt}
                    loading="lazy"
                    decoding="async"
                    onLoad={() => setLoaded(true)}
                    className={cn(
                        'h-full w-full object-cover',
                        'transition-[transform,opacity,filter] duration-[1.1s] [transition-timing-function:var(--ease-out-expo)]',
                        'group-hover/card:scale-[1.06] group-hover/img:scale-[1.06]',
                        loaded ? 'opacity-100 blur-0' : 'opacity-0 blur-sm',
                        imgClassName,
                    )}
                />
            ) : (
                <div className="blueprint-grid absolute inset-0 bg-ink-850" />
            )}
            {children}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* Gallery: a grid of tiles, any of which opens the shared lightbox.   */
/* ------------------------------------------------------------------ */
export function Gallery({ images = [], className, alt = '' }) {
    const [active, setActive] = useState(null);
    const { t } = useSite();

    if (!images.length) return null;

    return (
        <>
            <div className={cn('grid gap-3 sm:grid-cols-2 lg:grid-cols-3', className)}>
                {images.map((image, index) => (
                    <motion.div
                        key={image}
                        className={cn(
                            'group/tile relative overflow-hidden bg-ink-850',
                            index === 0 ? 'aspect-[16/11] sm:col-span-2' : 'aspect-[4/3]',
                        )}
                        initial={{ opacity: 0, y: 30 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, amount: 0.2 }}
                        transition={{ duration: 0.8, delay: index * 0.06, ease: EASE.outExpo }}
                    >
                        <img
                            src={image}
                            alt=""
                            loading="lazy"
                            className="h-full w-full object-cover transition-transform duration-[1.2s] [transition-timing-function:var(--ease-out-expo)] group-hover/tile:scale-105"
                        />
                        <ZoomTrigger onClick={() => setActive(index)} label={t('common.enlarge')} />
                    </motion.div>
                ))}
            </div>

            <AnimatePresence>
                {active !== null && (
                    <Lightbox
                        images={images}
                        index={active}
                        alt={alt}
                        onIndex={setActive}
                        onClose={() => setActive(null)}
                    />
                )}
            </AnimatePresence>
        </>
    );
}
