import { useMemo, useState } from 'react';
import { router } from '@inertiajs/react';
import { AnimatePresence, motion } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { useLocalePath, useSite } from '@/Hooks';
import { Icon, Section } from '@/Components/Ui';
import { ProjectCard } from '@/Components/Cards';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb } from '@/Sections/Shared';

const STATUSES = ['completed', 'ongoing', 'upcoming'];

export default function ProjectsIndex({ projects = [], categories = [], filters = {} }) {
    const { t } = useSite();
    const localize = useLocalePath();
    const [layout, setLayout] = useState('grid');

    const setFilter = (key, value) => {
        const next = { ...filters, [key]: filters[key] === value ? null : value };

        router.get(localize('/realisations'), Object.fromEntries(Object.entries(next).filter(([, v]) => v)), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const counts = useMemo(
        () => ({
            total: projects.length,
            completed: projects.filter((p) => p.status === 'completed').length,
            ongoing: projects.filter((p) => p.status === 'ongoing').length,
        }),
        [projects],
    );

    return (
        <>
            <PageHero
                title={t('projects.hero_title')}
                lead={t('projects.hero_lead')}
                image="/storage/heroes/projects.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.projects')}</Crumb>
                    </>
                }
            />

            {/* Filter bar */}
            <div className="sticky top-[4.5rem] z-40 border-b border-concrete-200 bg-concrete-50/90 backdrop-blur-xl">
                <div className="shell flex flex-col gap-4 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <div className="flex flex-wrap items-center gap-2">
                        <FilterChip
                            active={!filters.categorie}
                            onClick={() => setFilter('categorie', null)}
                            label={t('projects.filter_all')}
                            count={counts.total}
                        />
                        {categories.map((category) => (
                            <FilterChip
                                key={category.id}
                                active={filters.categorie === category.slug}
                                onClick={() => setFilter('categorie', category.slug)}
                                label={category.name}
                                count={category.projects_count}
                            />
                        ))}
                    </div>

                    <div className="flex items-center gap-4">
                        <div className="hidden items-center gap-2 sm:flex">
                            {STATUSES.map((status) => (
                                <button
                                    key={status}
                                    type="button"
                                    onClick={() => setFilter('statut', status)}
                                    className={cn(
                                        'whitespace-nowrap rounded-full border px-3.5 py-2 font-mono text-[0.625rem] uppercase tracking-[0.12em] transition-colors duration-400',
                                        filters.statut === status
                                            ? 'border-brand bg-brand text-ink-950'
                                            : 'border-concrete-300 text-concrete-500 hover:border-ink-900 hover:text-ink-900',
                                    )}
                                >
                                    {t(`projects.status.${status}`)}
                                </button>
                            ))}
                        </div>

                        <div className="hidden items-center border border-concrete-300 lg:flex">
                            {[
                                { key: 'grid', icon: 'layers' },
                                { key: 'list', icon: 'list-checks' },
                            ].map((option) => (
                                <button
                                    key={option.key}
                                    type="button"
                                    onClick={() => setLayout(option.key)}
                                    aria-label={option.key}
                                    className={cn(
                                        'flex size-9 items-center justify-center transition-colors duration-300',
                                        layout === option.key
                                            ? 'bg-ink-900 text-concrete-50'
                                            : 'text-concrete-500 hover:text-ink-900',
                                    )}
                                >
                                    <Icon name={option.icon} className="size-4" />
                                </button>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            <Section size="lg">
                <div className="shell">
                    <Reveal>
                        <p className="font-mono text-[0.6875rem] uppercase tracking-[0.16em] text-concrete-500">
                            {projects.length} {t('common.results')}
                            {counts.ongoing > 0 && (
                                <span className="ml-4 inline-flex items-center gap-2 text-brand-deep">
                                    <span className="relative flex size-1.5">
                                        <span className="absolute inline-flex h-full w-full animate-pulse-ring rounded-full bg-brand-deep" />
                                        <span className="relative inline-flex size-1.5 rounded-full bg-brand-deep" />
                                    </span>
                                    {counts.ongoing} {t('projects.status.ongoing')}
                                </span>
                            )}
                        </p>
                    </Reveal>

                    {projects.length === 0 ? (
                        <div className="mt-12 border border-dashed border-concrete-300 py-16 text-center">
                            <Icon name="search" className="mx-auto size-8 text-concrete-300" />
                            <p className="mt-5 text-sm text-concrete-500">{t('common.no_results')}</p>
                        </div>
                    ) : (
                        <AnimatePresence mode="wait">
                            <motion.div
                                key={`${layout}-${filters.categorie ?? 'all'}-${filters.statut ?? 'all'}`}
                                initial={{ opacity: 0, y: 16 }}
                                animate={{ opacity: 1, y: 0 }}
                                exit={{ opacity: 0, y: -8 }}
                                transition={{ duration: 0.5, ease: EASE.outExpo }}
                                className={cn(
                                    'mt-12',
                                    layout === 'grid'
                                        ? 'grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3'
                                        : 'grid gap-16',
                                )}
                            >
                                {projects.map((project, index) => (
                                    <ProjectCard
                                        key={project.id}
                                        project={project}
                                        index={index}
                                        size={layout === 'list' ? 'lg' : 'md'}
                                    />
                                ))}
                            </motion.div>
                        </AnimatePresence>
                    )}
                </div>
            </Section>

            <CtaBand
                title="Votre projet est le prochain"
                primary={{ href: '/devis', label: t('nav.quote') }}
            />
        </>
    );
}

function FilterChip({ active, onClick, label, count }) {
    return (
        <button
            type="button"
            onClick={onClick}
            className={cn(
                'group/chip relative whitespace-nowrap rounded-full border px-4 py-2 font-mono text-[0.625rem] font-semibold uppercase tracking-[0.12em] transition-all duration-400',
                active
                    ? 'border-ink-900 bg-ink-900 text-concrete-50'
                    : 'border-concrete-300 text-concrete-600 hover:border-ink-900 hover:text-ink-900',
            )}
        >
            {label}
            {count !== null && count !== undefined && (
                <span className={cn('ml-2', active ? 'text-brand' : 'text-concrete-400')}>{count}</span>
            )}
        </button>
    );
}
