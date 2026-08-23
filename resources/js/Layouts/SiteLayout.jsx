import { useEffect, useLayoutEffect } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { useSite } from '@/Hooks';
import { useSmoothScroll } from '@/Hooks/useSmoothScroll';
import { Header } from '@/Components/Header';
import { Footer } from '@/Components/Footer';
import {
    Cursor,
    FlashToast,
    FloatingActions,
    PageTransition,
    Preloader,
    ScrollProgress,
} from '@/Components/Chrome';

export default function SiteLayout({ children }) {
    const { url } = usePage();
    const { locale, seo } = useSite();

    useSmoothScroll();

    // Blade stamps <html lang> on the full page load, but that element sits
    // outside the Inertia root: switching language is an Inertia visit, so the
    // props swap while the attribute keeps its first value and the document
    // claims French while reading English. Assistive tech takes its
    // pronunciation from it, so it has to be kept in step from here.
    // Matches Blade's own normalisation, which turns fr_CM into fr-CM.
    useEffect(() => {
        document.documentElement.lang = String(locale).replace('_', '-');
    }, [locale]);

    // ScrollTrigger caches element positions and, where a section is pinned,
    // physically rewrites the DOM. Inertia swaps pages without a reload, so
    // every trigger has to be torn down and re-measured on each visit.
    useLayoutEffect(() => {
        const timer = window.setTimeout(() => ScrollTrigger.refresh(), 220);

        return () => {
            window.clearTimeout(timer);
            ScrollTrigger.getAll().forEach((trigger) => trigger.kill());
            gsap.globalTimeline.clear();
        };
    }, [url]);

    return (
        <>
            {/*
                The tab. Blade writes this string too, for the crawlers that
                never run the bundle, but Inertia takes the head over as soon
                as it boots and a visit after that is not a page load — so
                without this the first title stayed put while the reader
                walked the whole site. Set from the layout rather than from
                each page: the metadata is a shared prop, so one line here
                covers every route, including the record-backed ones.
            */}
            <Head title={seo.full_title} />

            <Preloader />
            <PageTransition />
            <ScrollProgress />
            <Cursor />

            <a
                href="#contenu"
                className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-100 focus:rounded-full focus:bg-ink-900 focus:px-6 focus:py-3 focus:text-sm focus:text-concrete-50"
            >
                Aller au contenu
            </a>

            <Header />

            {/*
                Keyed by URL so the page subtree remounts on every visit rather
                than being reconciled in place. Two things depend on it: GSAP
                gets a clean setup/teardown cycle instead of inheriting stale
                pinned markup, and scroll-triggered reveals replay on the new
                page instead of arriving already finished.
            */}
            <main id="contenu" key={url} className="relative">
                {children}
            </main>

            <Footer />

            <FloatingActions />
            <FlashToast />
        </>
    );
}
