import { useState } from 'react';
import { router } from '@inertiajs/react';
import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { cn } from '@/Lib/utils';
import { useLocalePath, useSite } from '@/Hooks';
import { Icon, Section, SectionHeading } from '@/Components/Ui';
import { PostCard } from '@/Components/Cards';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb } from '@/Sections/Shared';

export default function BlogIndex({ posts, featured, categories = [], filters = {} }) {
    const { t } = useSite();
    const localize = useLocalePath();
    const [search, setSearch] = useState(filters.q ?? '');

    const applyFilters = (next) => {
        const params = Object.fromEntries(
            Object.entries({ ...filters, ...next }).filter(([, value]) => value),
        );

        router.get(localize('/actualites'), params, { preserveState: true, preserveScroll: true, replace: true });
    };

    return (
        <>
            <PageHero
                title={t('blog.hero_title')}
                lead={t('blog.hero_lead')}
                image="/storage/heroes/blog.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.blog')}</Crumb>
                    </>
                }
            />

            {/* Toolbar */}
            <div className="sticky top-[4.5rem] z-40 border-b border-concrete-200 bg-concrete-50/90 backdrop-blur-xl">
                <div className="shell flex flex-col gap-4 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <div className="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            onClick={() => applyFilters({ rubrique: null })}
                            className={cn(
                                'whitespace-nowrap rounded-full border px-4 py-2 font-mono text-[0.625rem] font-semibold uppercase tracking-[0.12em] transition-all duration-400',
                                !filters.rubrique
                                    ? 'border-ink-900 bg-ink-900 text-concrete-50'
                                    : 'border-concrete-300 text-concrete-600 hover:border-ink-900 hover:text-ink-900',
                            )}
                        >
                            {t('common.all')}
                        </button>

                        {categories.map((category) => (
                            <button
                                key={category.id}
                                type="button"
                                onClick={() =>
                                    applyFilters({
                                        rubrique: filters.rubrique === category.slug ? null : category.slug,
                                    })
                                }
                                className={cn(
                                    'whitespace-nowrap rounded-full border px-4 py-2 font-mono text-[0.625rem] font-semibold uppercase tracking-[0.12em] transition-all duration-400',
                                    filters.rubrique === category.slug
                                        ? 'border-ink-900 bg-ink-900 text-concrete-50'
                                        : 'border-concrete-300 text-concrete-600 hover:border-ink-900 hover:text-ink-900',
                                )}
                            >
                                {category.name}
                                <span
                                    className={cn(
                                        'ml-2',
                                        filters.rubrique === category.slug ? 'text-brand' : 'text-concrete-400',
                                    )}
                                >
                                    {category.posts_count}
                                </span>
                            </button>
                        ))}
                    </div>

                    <form
                        onSubmit={(event) => {
                            event.preventDefault();
                            applyFilters({ q: search || null });
                        }}
                        className="flex min-w-0 items-center gap-2 border-b border-concrete-300 transition-colors focus-within:border-ink-900 lg:w-64"
                    >
                        <Icon name="search" className="size-4 shrink-0 text-concrete-400" />
                        <input
                            type="search"
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder={t('common.search')}
                            className="min-w-0 flex-1 bg-transparent py-2 text-sm outline-none placeholder:text-concrete-400"
                        />
                    </form>
                </div>
            </div>

            {/* Featured */}
            {featured && (
                <Section size="md">
                    <div className="shell">
                        <Reveal>
                            <span className="eyebrow text-concrete-500">{t('blog.featured')}</span>
                        </Reveal>
                        <div className="mt-10">
                            <PostCard post={featured} horizontal />
                        </div>
                    </div>
                </Section>
            )}

            {/* Grid */}
            <Section size={featured ? 'md' : 'lg'} className={featured ? 'pt-0' : ''}>
                <div className="shell">
                    <SectionHeading eyebrow={t('blog.latest')} title={t('blog.title')} />

                    {posts.data.length === 0 ? (
                        <div className="mt-10 border border-dashed border-concrete-300 py-16 text-center">
                            <Icon name="search" className="mx-auto size-8 text-concrete-300" />
                            <p className="mt-5 text-sm text-concrete-500">{t('common.no_results')}</p>
                        </div>
                    ) : (
                        <div className="mt-10 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                            {posts.data.map((post, index) => (
                                <PostCard key={post.id} post={post} index={index} />
                            ))}
                        </div>
                    )}

                    {posts.links?.length > 3 && (
                        <nav className="mt-14 flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
                            {posts.links.map((link, index) => (
                                <PaginationLink key={index} link={link} />
                            ))}
                        </nav>
                    )}
                </div>
            </Section>

            <CtaBand />
        </>
    );
}

function PaginationLink({ link }) {
    const label = link.label
        .replace('&laquo; Previous', '←')
        .replace('Next &raquo;', '→')
        .replace('pagination.previous', '←')
        .replace('pagination.next', '→');

    if (!link.url) {
        return (
            <span className="flex size-11 items-center justify-center font-mono text-xs text-concrete-300">
                {label}
            </span>
        );
    }

    return (
        <Link
            href={link.url}
            preserveScroll
            className={cn(
                'flex size-11 items-center justify-center border font-mono text-xs transition-colors duration-400',
                link.active
                    ? 'border-ink-900 bg-ink-900 text-concrete-50'
                    : 'border-concrete-300 text-concrete-600 hover:border-ink-900 hover:text-ink-900',
            )}
        >
            {label}
        </Link>
    );
}
