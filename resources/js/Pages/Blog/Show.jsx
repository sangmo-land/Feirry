import { useEffect, useState } from 'react';
import { Link } from '@/Components/Link';
import { motion, useScroll, useSpring } from 'motion/react';
import { cn, formatDate, initials } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { Icon, Section, SectionHeading } from '@/Components/Ui';
import { PostCard } from '@/Components/Cards';
import { ParallaxImage } from '@/Components/Media';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb } from '@/Sections/Shared';

export default function BlogShow({ post, related = [] }) {
    const { t, locale, site } = useSite();
    const [copied, setCopied] = useState(false);

    const { scrollYProgress } = useScroll();
    const progress = useSpring(scrollYProgress, { stiffness: 200, damping: 40 });

    const share = async () => {
        const url = window.location.href;

        if (navigator.share) {
            try {
                await navigator.share({ title: post.title, url });
                return;
            } catch {
                /* the visitor dismissed the share sheet */
            }
        }

        try {
            await navigator.clipboard.writeText(url);
            setCopied(true);
        } catch {
            /* clipboard blocked; nothing useful to do */
        }
    };

    useEffect(() => {
        if (!copied) return undefined;
        const timer = window.setTimeout(() => setCopied(false), 2400);
        return () => window.clearTimeout(timer);
    }, [copied]);

    return (
        <>
            <PageHero
                title={post.title}
                lead={post.excerpt}
                image={post.cover_image}
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb href="/actualites">{t('nav.blog')}</Crumb>
                        <Crumb last>{post.title}</Crumb>
                    </>
                }
            >
                <div className="flex flex-wrap items-center gap-x-6 gap-y-3 font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-concrete-400">
                    <span className="inline-flex items-center gap-2">
                        <span className="flex size-8 items-center justify-center rounded-full bg-brand font-display text-[0.625rem] font-black text-ink-950">
                            {initials(post.author?.name ?? site.brand?.name ?? 'SB')}
                        </span>
                        {t('blog.by')} {post.author?.name ?? site.brand?.name}
                    </span>
                    <time dateTime={post.published_at}>{formatDate(post.published_at, locale)}</time>
                    <span className="inline-flex items-center gap-1.5">
                        <Icon name="clock" className="size-3.5" />
                        {post.reading_minutes} {t('common.minutes_read')}
                    </span>
                    <span className="inline-flex items-center gap-1.5">
                        <Icon name="eye" className="size-3.5" />
                        {post.views}
                    </span>
                </div>
            </PageHero>

            {/* Reading progress */}
            <div className="sticky top-[4.5rem] z-40 h-0.5 bg-concrete-200">
                <motion.div style={{ scaleX: progress }} className="h-full origin-left bg-brand" />
            </div>

            <Section size="lg">
                <div className="shell grid gap-14 lg:grid-cols-12 lg:gap-16">
                    {/* Share rail */}
                    <aside className="min-w-0 lg:col-span-2">
                        <div className="flex gap-3 lg:sticky lg:top-36 lg:flex-col">
                            <button
                                type="button"
                                onClick={share}
                                aria-label={t('common.share')}
                                className="group/share relative flex size-11 items-center justify-center rounded-full border border-concrete-300 text-ink-900 transition-colors duration-400 hover:border-ink-900 hover:bg-ink-900 hover:text-concrete-50"
                            >
                                <Icon name={copied ? 'check' : 'share-2'} className="size-4" />
                            </button>

                            {copied && (
                                <motion.span
                                    initial={{ opacity: 0, x: -6 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    className="self-center font-mono text-[0.625rem] uppercase tracking-[0.14em] text-jade"
                                >
                                    {t('common.copied')}
                                </motion.span>
                            )}

                            {post.tags?.length > 0 && (
                                <div className="hidden pt-6 lg:block">
                                    <span className="block font-mono text-[0.625rem] uppercase tracking-[0.16em] text-concrete-400">
                                        Tags
                                    </span>
                                    <ul className="mt-4 space-y-2">
                                        {post.tags.map((tag) => (
                                            <li
                                                key={tag}
                                                className="font-mono text-[0.625rem] uppercase tracking-[0.1em] text-concrete-500"
                                            >
                                                #{tag}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </aside>

                    {/* Body */}
                    <article className="min-w-0 lg:col-span-8">
                        <Reveal>
                            <div
                                className={cn(
                                    'prose prose-brand prose-lg max-w-none',
                                    'prose-headings:font-display prose-headings:tracking-[-0.03em]',
                                    'prose-h2:mt-14 prose-h2:text-3xl prose-h3:mt-10 prose-h3:text-2xl',
                                    'prose-li:marker:text-brand',
                                    'prose-strong:font-semibold',
                                )}
                                dangerouslySetInnerHTML={{ __html: post.body }}
                            />
                        </Reveal>

                        <div className="mt-12 flex flex-wrap items-center justify-between gap-6 border-t border-concrete-200 pt-10">
                            <Link
                                href="/actualites"
                                className="group/back inline-flex items-center gap-2.5 font-mono text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-ink-900"
                            >
                                <Icon
                                    name="arrow-left"
                                    className="size-4 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/back:-translate-x-1.5"
                                    strokeWidth={2}
                                />
                                {t('common.back_to_blog')}
                            </Link>

                            <button
                                type="button"
                                onClick={share}
                                className="inline-flex items-center gap-2.5 font-mono text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-concrete-500 transition-colors hover:text-ink-900"
                            >
                                {copied ? t('common.copied') : t('common.share')}
                                <Icon name={copied ? 'check' : 'share-2'} className="size-4" strokeWidth={2} />
                            </button>
                        </div>
                    </article>

                    {/* Sidebar CTA */}
                    <aside className="min-w-0 lg:col-span-2">
                        <div className="lg:sticky lg:top-36">
                            <div className="border border-concrete-200 bg-concrete-100 p-6">
                                <Icon name="message-square" className="size-5 text-brand-deep" />
                                <p className="mt-4 font-display text-base font-bold leading-snug tracking-[-0.02em] text-ink-900">
                                    Un projet en tête ?
                                </p>
                                <p className="mt-2 text-xs leading-relaxed text-concrete-600">
                                    Un ingénieur vous rappelle sous 48 heures ouvrées.
                                </p>
                                <Link
                                    href="/devis"
                                    className="group/cta mt-5 inline-flex items-center gap-2 font-mono text-[0.625rem] font-semibold uppercase tracking-[0.14em] text-ink-900"
                                >
                                    {t('nav.quote')}
                                    <Icon
                                        name="arrow-right"
                                        className="size-3.5 transition-transform duration-500 group-hover/cta:translate-x-1"
                                        strokeWidth={2}
                                    />
                                </Link>
                            </div>
                        </div>
                    </aside>
                </div>
            </Section>

            {post.cover_image && (
                <ParallaxImage
                    src={post.cover_image}
                    alt={post.title}
                    className="h-[38vh] sm:h-[52vh]"
                    strength={10}
                    zoomable
                />
            )}

            {related.length > 0 && (
                <Section tone="muted" size="lg">
                    <div className="shell">
                        <SectionHeading eyebrow={t('blog.related')} title={t('blog.latest')} />
                        <div className="mt-10 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                            {related.map((item, index) => (
                                <PostCard key={item.id} post={item} index={index} />
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            <CtaBand />
        </>
    );
}
