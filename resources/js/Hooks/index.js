import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';
import { localizePath } from '@/Lib/routes';
import { translate } from '@/Lib/utils';

/** Shared Inertia props, typed loosely but consistently across the app. */
export function useSite() {
    const { props } = usePage();

    return useMemo(
        () => ({
            site: props.site ?? {},
            // The metadata the controller set for this page. It is what the
            // layout puts in the tab, so it has to travel with the visit
            // rather than being read once out of the Blade head.
            seo: props.seo ?? {},
            // The current page's editable hero, or null on a page whose hero
            // comes from its own record.
            hero: props.hero ?? null,
            locale: props.locale ?? 'fr',
            locales: props.locales ?? {},
            flash: props.flash ?? {},
            t: (key, replacements) => translate(props.translations ?? {}, key, replacements),
        }),
        [props.site, props.seo, props.hero, props.locale, props.locales, props.flash, props.translations],
    );
}

/**
 * Renders an internal path in the active language.
 *
 * Hrefs are written in French throughout the app — the canonical spelling,
 * and the one the route file registers unprefixed — and pass through here on
 * the way into the DOM. `Components/Link` and `Ui.Button` already apply it,
 * so this is only needed where a path is handed somewhere else: a form's
 * action, a router.visit, a redirect.
 *
 * Pass a locale to render the path in a language other than the active one —
 * which is how the switcher points at the current page in the other tongue.
 */
export function useLocalePath() {
    const { props } = usePage();

    return useCallback(
        (href, locale = props.locale ?? 'fr') =>
            localizePath(href, locale, {
                segments: props.segments ?? {},
                locales: props.locales ?? {},
                defaultLocale: props.defaultLocale ?? 'fr',
            }),
        [props.locale, props.segments, props.locales, props.defaultLocale],
    );
}

/**
 * Holds the page still while something is laid over it.
 *
 * Lenis drives the scroll, so `overflow: hidden` alone does not stop it — the
 * smooth-scroll instance has to be told to stand down as well, or the page
 * carries on gliding behind the overlay.
 */
export function useScrollLock(active) {
    useEffect(() => {
        if (!active) return undefined;

        const previous = document.body.style.overflow;

        document.body.style.overflow = 'hidden';
        window.__lenis?.stop();

        return () => {
            document.body.style.overflow = previous;
            window.__lenis?.start();
        };
    }, [active]);
}

export function useMediaQuery(query) {
    const [matches, setMatches] = useState(() =>
        typeof window === 'undefined' ? false : window.matchMedia(query).matches,
    );

    useEffect(() => {
        const list = window.matchMedia(query);
        const handler = (event) => setMatches(event.matches);

        setMatches(list.matches);
        list.addEventListener('change', handler);

        return () => list.removeEventListener('change', handler);
    }, [query]);

    return matches;
}

export function useReducedMotion() {
    return useMediaQuery('(prefers-reduced-motion: reduce)');
}

/** True once the element has entered the viewport; never flips back. */
export function useInViewOnce(options = {}) {
    const ref = useRef(null);
    const [inView, setInView] = useState(false);

    useEffect(() => {
        const node = ref.current;
        if (!node || inView) return undefined;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setInView(true);
                    observer.disconnect();
                }
            },
            { threshold: 0.25, rootMargin: '0px 0px -8% 0px', ...options },
        );

        observer.observe(node);

        return () => observer.disconnect();
    }, [inView, options.threshold, options.rootMargin]);

    return [ref, inView];
}

/** Counts from 0 to `target` with an ease-out curve, once visible. */
export function useCountUp(target, { duration = 1800, start = false } = {}) {
    const [value, setValue] = useState(0);

    useEffect(() => {
        if (!start) return undefined;

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            setValue(target);
            return undefined;
        }

        let frame;
        const began = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - began) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 4);

            setValue(Math.round(target * eased));

            if (progress < 1) frame = requestAnimationFrame(tick);
        };

        frame = requestAnimationFrame(tick);

        return () => cancelAnimationFrame(frame);
    }, [target, duration, start]);

    return value;
}

/** Pulls an element gently toward the pointer. Disabled on touch devices. */
export function useMagnetic(strength = 0.28) {
    const ref = useRef(null);
    const isFine = useMediaQuery('(hover: hover) and (pointer: fine)');

    useEffect(() => {
        const node = ref.current;
        if (!node || !isFine) return undefined;

        let frame;

        const move = (event) => {
            const rect = node.getBoundingClientRect();
            const x = (event.clientX - (rect.left + rect.width / 2)) * strength;
            const y = (event.clientY - (rect.top + rect.height / 2)) * strength;

            cancelAnimationFrame(frame);
            frame = requestAnimationFrame(() => {
                node.style.transform = `translate3d(${x}px, ${y}px, 0)`;
            });
        };

        const reset = () => {
            cancelAnimationFrame(frame);
            node.style.transform = 'translate3d(0, 0, 0)';
        };

        node.addEventListener('pointermove', move);
        node.addEventListener('pointerleave', reset);

        return () => {
            cancelAnimationFrame(frame);
            node.removeEventListener('pointermove', move);
            node.removeEventListener('pointerleave', reset);
        };
    }, [strength, isFine]);

    return ref;
}

/** Window scroll position in pixels, throttled to animation frames. */
export function useScrollPosition() {
    const [position, setPosition] = useState(0);

    useEffect(() => {
        let frame;

        const onScroll = () => {
            cancelAnimationFrame(frame);
            frame = requestAnimationFrame(() => setPosition(window.scrollY));
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        return () => {
            cancelAnimationFrame(frame);
            window.removeEventListener('scroll', onScroll);
        };
    }, []);

    return position;
}
