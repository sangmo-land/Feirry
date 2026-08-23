import { useLayoutEffect, useRef } from 'react';
import { Link } from '@/Components/Link';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { cn, formatNumber } from '@/Lib/utils';
import { useMediaQuery, useReducedMotion, useSite } from '@/Hooks';
import { Button, Eyebrow, Icon } from '@/Components/Ui';
import { ProjectCard } from '@/Components/Cards';
import { Reveal, SplitText } from '@/Components/Motion';

gsap.registerPlugin(ScrollTrigger);

/**
 * The signature scroll moment: the section pins and the project rail travels
 * sideways as the visitor scrolls down. Below `lg`, and whenever reduced
 * motion is requested, it degrades to an ordinary responsive grid.
 */
export function HorizontalProjects({ projects = [] }) {
    const { t, locale } = useSite();
    const sectionRef = useRef(null);
    const railRef = useRef(null);
    const progressRef = useRef(null);

    const isDesktop = useMediaQuery('(min-width: 1024px)');
    const reduced = useReducedMotion();
    const enabled = isDesktop && !reduced && projects.length > 2;

    // useLayoutEffect, not useEffect: ScrollTrigger's pin wraps this section
    // in a pin-spacer element React knows nothing about. Layout-effect cleanup
    // runs before React removes the host node, so the context can revert the
    // wrapper first. With a passive effect the cleanup lands too late and
    // React throws "removeChild: node is not a child of this node".
    useLayoutEffect(() => {
        if (!enabled) return undefined;

        const section = sectionRef.current;
        const rail = railRef.current;
        const bar = progressRef.current;
        if (!section || !rail) return undefined;

        const context = gsap.context(() => {
            const distance = () => Math.max(0, rail.scrollWidth - window.innerWidth + 96);

            const tween = gsap.to(rail, {
                x: () => -distance(),
                ease: 'none',
                scrollTrigger: {
                    trigger: section,
                    start: 'top top',
                    end: () => `+=${distance() + window.innerHeight * 0.4}`,
                    pin: true,
                    scrub: 0.85,
                    anticipatePin: 1,
                    invalidateOnRefresh: true,
                    onUpdate: (self) => {
                        if (bar) bar.style.transform = `scaleX(${self.progress})`;
                    },
                },
            });

            return () => tween.kill();
        }, section);

        return () => context.revert();
    }, [enabled, projects.length]);

    if (!projects.length) return null;

    if (!enabled) {
        return (
            <section className="bg-concrete-50 py-16 sm:py-20">
                <div className="shell">
                    <Header t={t} />
                    <div className="mt-10 grid gap-10 sm:grid-cols-2">
                        {projects.map((project, index) => (
                            <ProjectCard key={project.id} project={project} index={index} />
                        ))}
                    </div>
                    <div className="mt-10">
                        <Button href="/realisations" tone="outline">
                            {t('common.view_all_projects')}
                        </Button>
                    </div>
                </div>
            </section>
        );
    }

    return (
        <section ref={sectionRef} className="relative overflow-hidden bg-concrete-50">
            <div className="flex h-screen flex-col justify-center py-14">
                <div className="shell shrink-0">
                    <Header t={t} withAction />
                </div>

                <div className="relative mt-12 min-h-0 flex-1">
                    <div ref={railRef} className="flex h-full items-stretch gap-8 pl-[max(1.25rem,calc((100vw-88rem)/2+3.5rem))] pr-24 will-change-transform">
                        {projects.map((project, index) => (
                            <HorizontalCard key={project.id} project={project} index={index} locale={locale} t={t} />
                        ))}

                        <Link
                            href="/realisations"
                            className="group/end flex w-[22rem] shrink-0 flex-col items-center justify-center gap-6 border border-dashed border-concrete-300 transition-colors duration-500 hover:border-ink-900"
                        >
                            <span className="flex size-20 items-center justify-center rounded-full bg-ink-900 text-concrete-50 transition-transform duration-700 [transition-timing-function:var(--ease-out-expo)] group-hover/end:scale-110">
                                <Icon name="arrow-right" className="size-7" strokeWidth={1.8} />
                            </span>
                            <span className="max-w-[12rem] text-center font-display text-xl font-bold tracking-[-0.02em] text-ink-900">
                                {t('common.view_all_projects')}
                            </span>
                        </Link>
                    </div>
                </div>

                <div className="shell mt-10 shrink-0">
                    <div className="h-px w-full bg-concrete-200">
                        <div ref={progressRef} className="h-full origin-left scale-x-0 bg-brand" />
                    </div>
                    <div className="mt-4 flex items-center justify-between font-mono text-[0.625rem] uppercase tracking-[0.18em] text-concrete-500">
                        <span className="inline-flex items-center gap-2">
                            <Icon name="mouse" className="size-3.5" />
                            {t('common.scroll')}
                        </span>
                        <span>
                            {String(projects.length).padStart(2, '0')} {t('nav.projects')}
                        </span>
                    </div>
                </div>
            </div>
        </section>
    );
}

function Header({ t, withAction = false }) {
    return (
        <div className={cn('flex flex-col gap-6', withAction && 'lg:flex-row lg:items-end lg:justify-between')}>
            <div className="max-w-2xl">
                <Reveal y={14}>
                    <Eyebrow>{t('home.projects_eyebrow')}</Eyebrow>
                </Reveal>
                <h2 className="mt-5 text-[clamp(2rem,5vw,4rem)] text-ink-900">
                    <SplitText text={t('home.projects_title')} />
                </h2>
                <Reveal delay={0.12}>
                    <p className="mt-5 max-w-xl text-sm leading-relaxed text-concrete-600 sm:text-base">
                        {t('home.projects_lead')}
                    </p>
                </Reveal>
            </div>

            {withAction && (
                <Reveal delay={0.2} className="shrink-0">
                    <Button href="/realisations" tone="outline">
                        {t('common.view_all_projects')}
                    </Button>
                </Reveal>
            )}
        </div>
    );
}

function HorizontalCard({ project, index, locale, t }) {
    return (
        <article className="group/card relative flex w-[min(78vw,32rem)] shrink-0 flex-col">
            <Link href={`/realisations/${project.slug}`} className="flex min-h-0 flex-1 flex-col">
                <div className="relative min-h-0 flex-1 overflow-hidden bg-ink-850">
                    {project.cover_image ? (
                        <img
                            src={project.cover_image}
                            alt={project.title}
                            loading={index < 2 ? 'eager' : 'lazy'}
                            className="h-full w-full object-cover transition-transform duration-[1.3s] [transition-timing-function:var(--ease-out-expo)] group-hover/card:scale-105"
                        />
                    ) : (
                        <div className="blueprint-grid h-full w-full" />
                    )}

                    <div className="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950/90 to-transparent" />

                    <span className="absolute left-6 top-6 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-concrete-300">
                        {String(index + 1).padStart(2, '0')} — {project.year}
                    </span>

                    <span className="absolute right-6 top-6 flex size-11 translate-y-2 items-center justify-center rounded-full bg-brand text-ink-950 opacity-0 transition-all duration-700 [transition-timing-function:var(--ease-out-expo)] group-hover/card:translate-y-0 group-hover/card:opacity-100">
                        <Icon name="arrow-up-right" className="size-5" strokeWidth={2.2} />
                    </span>

                    <div className="absolute inset-x-0 bottom-0 p-7">
                        {project.category && (
                            <span className="eyebrow text-brand">{project.category.name}</span>
                        )}
                        <h3 className="mt-2.5 text-[clamp(1.4rem,2.2vw,2rem)] text-concrete-50">{project.title}</h3>
                        <p className="mt-2 flex items-center gap-2 font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-400">
                            <Icon name="map-pin" className="size-3.5" />
                            {project.location}
                        </p>
                    </div>
                </div>
            </Link>

            <dl className="mt-5 flex shrink-0 gap-x-8 border-t border-concrete-200 pt-4 font-mono text-[0.625rem] uppercase tracking-[0.12em]">
                {project.surface_m2 && (
                    <div>
                        <dt className="text-concrete-400">{t('projects.surface')}</dt>
                        <dd className="mt-0.5 text-ink-800">{formatNumber(project.surface_m2, locale)} m²</dd>
                    </div>
                )}
                {project.duration_months && (
                    <div>
                        <dt className="text-concrete-400">{t('projects.duration')}</dt>
                        <dd className="mt-0.5 text-ink-800">
                            {project.duration_months} {t('projects.months')}
                        </dd>
                    </div>
                )}
                {project.budget && (
                    <div>
                        <dt className="text-concrete-400">{t('projects.budget')}</dt>
                        <dd className="mt-0.5 text-ink-800">{project.budget}</dd>
                    </div>
                )}
            </dl>
        </article>
    );
}
