import { useEffect, useRef } from 'react';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/**
 * One Lenis instance for the whole app, driven by GSAP's ticker so that
 * ScrollTrigger and the smooth scroller stay on the same clock. Without this
 * they drift and pinned sections jitter.
 */
export function useSmoothScroll() {
    const lenisRef = useRef(null);

    useEffect(() => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return undefined;
        }

        const lenis = new Lenis({
            duration: 1.1,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
            wheelMultiplier: 0.95,
            touchMultiplier: 1.6,
            syncTouch: false,
        });

        lenisRef.current = lenis;
        window.__lenis = lenis;

        lenis.on('scroll', ScrollTrigger.update);

        const raf = (time) => lenis.raf(time * 1000);
        gsap.ticker.add(raf);
        gsap.ticker.lagSmoothing(0);

        return () => {
            gsap.ticker.remove(raf);
            lenis.destroy();
            lenisRef.current = null;
            delete window.__lenis;
        };
    }, []);

    return lenisRef;
}

/** Jump to top without the smooth easing, used between Inertia pages. */
export function scrollToTop() {
    if (window.__lenis) {
        window.__lenis.scrollTo(0, { immediate: true });
        return;
    }
    window.scrollTo(0, 0);
}

export function scrollToAnchor(selector, offset = -100) {
    const target = document.querySelector(selector);
    if (!target) return;

    if (window.__lenis) {
        window.__lenis.scrollTo(target, { offset, duration: 1.2 });
        return;
    }

    window.scrollTo({ top: target.offsetTop + offset, behavior: 'smooth' });
}
