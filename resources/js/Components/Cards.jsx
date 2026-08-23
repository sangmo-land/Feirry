import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { cn, EASE, formatDate, formatNumber } from '@/Lib/utils';
import { useCountUp, useInViewOnce, useSite } from '@/Hooks';
import { Badge, Icon, Monogram } from '@/Components/Ui';
import { HoverImage } from '@/Components/Media';

/* ------------------------------------------------------------------ */
/* ProjectCard                                                         */
/* ------------------------------------------------------------------ */
export function ProjectCard({ project, index = 0, size = 'md' }) {
    const { t, locale } = useSite();

    const statusTone = {
        completed: 'neutral',
        ongoing: 'brand',
        upcoming: 'jade',
    }[project.status];

    return (
        <motion.article
            className="group/card relative"
            initial={{ opacity: 0, y: 40 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.15 }}
            transition={{ duration: 0.9, delay: (index % 3) * 0.09, ease: EASE.outExpo }}
        >
            <Link href={`/realisations/${project.slug}`} className="block">
                <HoverImage
                    src={project.cover_image}
                    alt={project.title}
                    ratio={size === 'lg' ? 'aspect-[16/10]' : 'aspect-[4/3]'}
                >
                    <div className="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950/85 to-transparent opacity-90" />

                    <div className="absolute left-5 top-5 flex flex-wrap gap-2">
                        <Badge tone={statusTone} className="backdrop-blur-sm">
                            {project.status === 'ongoing' && (
                                <span className="relative flex size-1.5">
                                    <span className="absolute inline-flex h-full w-full animate-pulse-ring rounded-full bg-brand-deep" />
                                    <span className="relative inline-flex size-1.5 rounded-full bg-brand-deep" />
                                </span>
                            )}
                            {t(`projects.status.${project.status}`)}
                            {project.status === 'ongoing' ? ` ${project.progress}%` : ''}
                        </Badge>
                    </div>

                    <div className="absolute inset-x-0 bottom-0 p-5 sm:p-7">
                        {project.category && (
                            <span className="eyebrow text-brand">{project.category.name}</span>
                        )}
                        <h3
                            className={cn(
                                'mt-2.5 text-concrete-50',
                                size === 'lg' ? 'text-[clamp(1.5rem,2.6vw,2.5rem)]' : 'text-2xl',
                            )}
                        >
                            {project.title}
                        </h3>
                        <p className="mt-2 flex items-center gap-2 font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-concrete-300">
                            <Icon name="map-pin" className="size-3.5" />
                            {project.location}
                            {project.year && (
                                <>
                                    <span className="text-concrete-500">/</span>
                                    {project.year}
                                </>
                            )}
                        </p>
                    </div>

                    <span className="absolute right-5 top-5 flex size-11 translate-y-2 items-center justify-center rounded-full bg-brand text-ink-950 opacity-0 transition-all duration-[0.7s] [transition-timing-function:var(--ease-out-expo)] group-hover/card:translate-y-0 group-hover/card:opacity-100">
                        <Icon name="arrow-up-right" className="size-5" strokeWidth={2.2} />
                    </span>
                </HoverImage>
            </Link>

            {size === 'lg' && project.excerpt && (
                <p className="mt-5 max-w-xl text-sm leading-relaxed text-concrete-600">{project.excerpt}</p>
            )}

            {(project.surface_m2 || project.duration_months || project.budget) && (
                <dl className="mt-4 flex flex-wrap gap-x-8 gap-y-2 border-t border-concrete-200 pt-4 font-mono text-[0.6875rem] uppercase tracking-[0.12em]">
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
            )}
        </motion.article>
    );
}

/* ------------------------------------------------------------------ */
/* ServiceCard                                                         */
/* ------------------------------------------------------------------ */
export function ServiceCard({ service, index = 0 }) {
    const { t } = useSite();

    return (
        <motion.article
            initial={{ opacity: 0, y: 36 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.2 }}
            transition={{ duration: 0.85, delay: (index % 3) * 0.1, ease: EASE.outExpo }}
            className="group/card relative"
        >
            <Link
                href={`/metiers/${service.slug}`}
                className="relative flex h-full flex-col justify-between overflow-hidden border border-concrete-200 bg-concrete-50 p-8 transition-colors duration-700 hover:border-ink-900 sm:p-10"
            >
                <span className="pointer-events-none absolute inset-0 z-0 translate-y-full bg-ink-900 transition-transform duration-[0.9s] [transition-timing-function:var(--ease-out-expo)] group-hover/card:translate-y-0" />

                <div className="relative z-10">
                    <div className="flex items-start justify-between gap-6">
                        <span className="flex size-14 items-center justify-center border border-concrete-300 text-ink-900 transition-colors duration-700 group-hover/card:border-brand group-hover/card:bg-brand">
                            <Icon name={service.icon} className="size-6" />
                        </span>
                        <span className="font-mono text-xs tracking-[0.2em] text-concrete-400 transition-colors duration-700 group-hover/card:text-concrete-500">
                            {String(index + 1).padStart(2, '0')}
                        </span>
                    </div>

                    <h3 className="mt-8 text-[1.75rem] text-ink-900 transition-colors duration-700 group-hover/card:text-concrete-50">
                        {service.title}
                    </h3>

                    {service.tagline && (
                        <p className="mt-2 font-mono text-[0.6875rem] uppercase tracking-[0.16em] text-brand-deep transition-colors duration-700 group-hover/card:text-brand">
                            {service.tagline}
                        </p>
                    )}

                    <p className="mt-5 line-clamp-4 text-sm leading-relaxed text-concrete-600 transition-colors duration-700 group-hover/card:text-concrete-300">
                        {service.excerpt}
                    </p>
                </div>

                <div className="relative z-10 mt-10 flex items-center justify-between border-t border-concrete-200 pt-6 transition-colors duration-700 group-hover/card:border-ink-700">
                    <span className="font-mono text-[0.6875rem] uppercase tracking-[0.16em] text-concrete-500 transition-colors duration-700 group-hover/card:text-concrete-400">
                        {service.starting_price
                            ? `${t('services.starting_from')} ${service.starting_price}`
                            : t('common.read_more')}
                    </span>
                    <span className="flex size-9 items-center justify-center rounded-full border border-concrete-300 text-ink-900 transition-all duration-700 group-hover/card:border-brand group-hover/card:bg-brand group-hover/card:text-ink-950">
                        <Icon name="arrow-right" className="size-4" strokeWidth={2} />
                    </span>
                </div>
            </Link>
        </motion.article>
    );
}

/* ------------------------------------------------------------------ */
/* PostCard                                                            */
/* ------------------------------------------------------------------ */
export function PostCard({ post, index = 0, horizontal = false }) {
    const { t, locale } = useSite();

    return (
        <motion.article
            initial={{ opacity: 0, y: 34 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.2 }}
            transition={{ duration: 0.85, delay: (index % 3) * 0.08, ease: EASE.outExpo }}
            className={cn('group/card', horizontal && 'grid gap-8 lg:grid-cols-2 lg:items-center')}
        >
            <Link href={`/actualites/${post.slug}`} className="block">
                <HoverImage
                    src={post.cover_image}
                    alt={post.title}
                    ratio={horizontal ? 'aspect-[16/10]' : 'aspect-[16/11]'}
                />
            </Link>

            <div className={cn(!horizontal && 'mt-6')}>
                <div className="flex flex-wrap items-center gap-3 font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-concrete-500">
                    {post.category && (
                        <span className="text-brand-deep" style={{ color: post.category.color }}>
                            {post.category.name}
                        </span>
                    )}
                    <span className="text-concrete-300">/</span>
                    <time dateTime={post.published_at}>{formatDate(post.published_at, locale)}</time>
                    <span className="text-concrete-300">/</span>
                    <span>
                        {post.reading_minutes} {t('common.minutes_read')}
                    </span>
                </div>

                <h3
                    className={cn(
                        'mt-3 text-ink-900',
                        horizontal ? 'text-[clamp(1.6rem,3vw,2.6rem)]' : 'text-2xl',
                    )}
                >
                    <Link href={`/actualites/${post.slug}`} className="link-draw">
                        {post.title}
                    </Link>
                </h3>

                <p
                    className={cn(
                        'mt-4 text-sm leading-relaxed text-concrete-600',
                        horizontal ? 'max-w-xl' : 'line-clamp-3',
                    )}
                >
                    {post.excerpt}
                </p>

                <Link
                    href={`/actualites/${post.slug}`}
                    className="mt-5 inline-flex items-center gap-2 font-mono text-[0.6875rem] font-semibold uppercase tracking-[0.16em] text-ink-900"
                >
                    {t('common.read_more')}
                    <Icon
                        name="arrow-right"
                        className="size-4 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/card:translate-x-1.5"
                        strokeWidth={2}
                    />
                </Link>
            </div>
        </motion.article>
    );
}

/* ------------------------------------------------------------------ */
/* TeamCard                                                            */
/* ------------------------------------------------------------------ */
export function TeamCard({ member, index = 0 }) {
    const { t } = useSite();

    return (
        <motion.article
            initial={{ opacity: 0, y: 34 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.2 }}
            transition={{ duration: 0.85, delay: (index % 4) * 0.08, ease: EASE.outExpo }}
            className="group/card"
        >
            <div className="relative aspect-[4/5] overflow-hidden">
                <Monogram name={member.name} photo={member.photo} />
                <div className="absolute inset-0 flex translate-y-4 flex-col justify-end bg-gradient-to-t from-ink-950/95 via-ink-950/40 to-transparent p-6 opacity-0 transition-all duration-[0.8s] [transition-timing-function:var(--ease-out-expo)] group-hover/card:translate-y-0 group-hover/card:opacity-100">
                    {member.bio && (
                        <p className="line-clamp-5 text-xs leading-relaxed text-concrete-200">{member.bio}</p>
                    )}
                    <div className="mt-4 flex gap-2">
                        {member.email && (
                            <a
                                href={`mailto:${member.email}`}
                                className="flex size-9 items-center justify-center rounded-full border border-concrete-50/30 text-concrete-50 transition-colors hover:bg-brand hover:text-ink-950"
                                aria-label={`Email ${member.name}`}
                            >
                                <Icon name="mail" className="size-4" />
                            </a>
                        )}
                        {member.linkedin && (
                            <a
                                href={member.linkedin}
                                target="_blank"
                                rel="noreferrer"
                                className="flex size-9 items-center justify-center rounded-full border border-concrete-50/30 text-concrete-50 transition-colors hover:bg-brand hover:text-ink-950"
                                aria-label={`LinkedIn ${member.name}`}
                            >
                                <Icon name="linkedin" className="size-4" />
                            </a>
                        )}
                    </div>
                </div>
            </div>

            <div className="mt-5">
                <h3 className="text-xl text-ink-900">{member.name}</h3>
                <p className="mt-1 font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-brand-deep">
                    {member.role}
                </p>
                {member.years_experience && (
                    <p className="mt-2 text-xs text-concrete-500">
                        {member.years_experience} {t('team.years_exp')}
                    </p>
                )}
                {member.expertise?.length > 0 && (
                    <ul className="mt-3 flex flex-wrap gap-1.5">
                        {member.expertise.map((item) => (
                            <li
                                key={item}
                                className="border border-concrete-200 px-2 py-1 text-[0.625rem] uppercase tracking-[0.1em] text-concrete-500"
                            >
                                {item}
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </motion.article>
    );
}

/* ------------------------------------------------------------------ */
/* StatBlock: counts up when it enters the viewport.                   */
/* ------------------------------------------------------------------ */
export function StatBlock({ stat, index = 0, tone = 'dark' }) {
    const [ref, inView] = useInViewOnce({ threshold: 0.4 });
    const value = useCountUp(stat.value, { start: inView, duration: 1600 + index * 180 });

    return (
        <motion.div
            ref={ref}
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.4 }}
            transition={{ duration: 0.8, delay: index * 0.08, ease: EASE.outExpo }}
            className={cn(
                'relative border-t pt-6',
                tone === 'dark' ? 'border-concrete-200' : 'border-ink-700',
            )}
        >
            <span
                className={cn(
                    'block font-display text-[clamp(2.75rem,6vw,4.75rem)] font-black leading-none tracking-tighter tabular-nums',
                    tone === 'dark' ? 'text-ink-900' : 'text-concrete-50',
                )}
            >
                {value}
                <span className="text-brand">{stat.suffix}</span>
            </span>
            <span
                className={cn(
                    'mt-3 block max-w-[14rem] font-mono text-[0.6875rem] uppercase leading-relaxed tracking-[0.14em]',
                    tone === 'dark' ? 'text-concrete-500' : 'text-concrete-400',
                )}
            >
                {stat.label}
            </span>
        </motion.div>
    );
}

/* ------------------------------------------------------------------ */
/* JobCard                                                             */
/* ------------------------------------------------------------------ */
export function JobCard({ job, index = 0 }) {
    const { t } = useSite();

    return (
        <motion.article
            initial={{ opacity: 0, y: 26 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.3 }}
            transition={{ duration: 0.75, delay: index * 0.06, ease: EASE.outExpo }}
        >
            <Link
                href={`/carrieres/${job.slug}`}
                className="group/card relative flex flex-col gap-4 border-b border-concrete-200 py-8 transition-colors hover:border-ink-900 md:flex-row md:items-center md:justify-between md:gap-10"
            >
                <div className="min-w-0 flex-1">
                    <h3 className="text-[clamp(1.35rem,2.4vw,2rem)] text-ink-900">{job.title}</h3>
                    <div className="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-concrete-500">
                        {job.department && (
                            <span className="inline-flex items-center gap-1.5">
                                <Icon name="layers" className="size-3.5" />
                                {job.department}
                            </span>
                        )}
                        <span className="inline-flex items-center gap-1.5">
                            <Icon name="map-pin" className="size-3.5" />
                            {job.location}
                        </span>
                        <span className="inline-flex items-center gap-1.5">
                            <Icon name="file-text" className="size-3.5" />
                            {job.contract_type}
                        </span>
                        {job.experience_level && (
                            <span className="inline-flex items-center gap-1.5">
                                <Icon name="trending-up" className="size-3.5" />
                                {job.experience_level}
                            </span>
                        )}
                    </div>
                </div>

                <div className="flex shrink-0 items-center gap-5">
                    {job.salary_range && (
                        <span className="hidden text-sm text-concrete-600 lg:block">{job.salary_range}</span>
                    )}
                    <span className="inline-flex items-center gap-2 rounded-full border border-ink-900/20 px-5 py-2.5 font-mono text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-ink-900 transition-colors duration-500 group-hover/card:border-ink-900 group-hover/card:bg-ink-900 group-hover/card:text-concrete-50">
                        {t('careers.apply')}
                        <Icon name="arrow-right" className="size-3.5" strokeWidth={2} />
                    </span>
                </div>
            </Link>
        </motion.article>
    );
}
