import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { formatNumber } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { Badge, Button, Icon, Section, SectionHeading } from '@/Components/Ui';
import { ProjectCard } from '@/Components/Cards';
import { Gallery, ParallaxImage } from '@/Components/Media';
import { PullQuote } from '@/Components/Testimonials';
import { Bar, Reveal, SplitText } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb } from '@/Sections/Shared';

export default function ProjectShow({ project, testimonial, similar = [], nextProject }) {
    const { t, locale } = useSite();

    const facts = [
        { label: t('projects.client'), value: project.client_name, icon: 'briefcase' },
        { label: t('projects.location'), value: project.location, icon: 'map-pin' },
        {
            label: t('projects.surface'),
            value: project.surface_m2 ? `${formatNumber(project.surface_m2, locale)} m²` : null,
            icon: 'square',
        },
        {
            label: t('projects.duration'),
            value: project.duration_months ? `${project.duration_months} ${t('projects.months')}` : null,
            icon: 'clock',
        },
        { label: t('projects.budget'), value: project.budget, icon: 'banknote' },
        { label: t('projects.year'), value: project.year, icon: 'calendar' },
        { label: t('projects.category'), value: project.category?.name, icon: 'layers' },
    ].filter((fact) => fact.value);

    const narrative = [
        { key: 'challenge', value: project.challenge, icon: 'target' },
        { key: 'solution', value: project.solution, icon: 'compass' },
        { key: 'outcome', value: project.outcome, icon: 'badge-check' },
    ].filter((block) => block.value);

    return (
        <>
            <PageHero
                title={project.title}
                lead={project.excerpt}
                image={project.cover_image}
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb href="/realisations">{t('nav.projects')}</Crumb>
                        <Crumb last>{project.title}</Crumb>
                    </>
                }
            >
                <div className="flex flex-wrap items-center gap-3">
                    <Badge tone={project.status === 'ongoing' ? 'brand' : 'outline'}>
                        {t(`projects.status.${project.status}`)}
                        {project.status === 'ongoing' ? ` · ${project.progress}%` : ''}
                    </Badge>
                    {project.year && <Badge tone="outline">{project.year}</Badge>}
                    {project.city && <Badge tone="outline">{project.city}</Badge>}
                </div>
            </PageHero>

            {/* Fact sheet */}
            <section className="border-b border-concrete-200 bg-concrete-100">
                <div className="shell">
                    <dl className="grid gap-px bg-concrete-200 sm:grid-cols-2 lg:grid-cols-4">
                        {facts.map((fact, index) => (
                            <motion.div
                                key={fact.label}
                                initial={{ opacity: 0, y: 18 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, amount: 0.5 }}
                                transition={{ duration: 0.6, delay: index * 0.05 }}
                                className="bg-concrete-100 p-6 sm:p-8"
                            >
                                <dt className="flex items-center gap-2 font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-500">
                                    <Icon name={fact.icon} className="size-3.5 text-brand-deep" />
                                    {fact.label}
                                </dt>
                                <dd className="mt-2.5 font-display text-lg font-bold tracking-[-0.02em] text-ink-900">
                                    {fact.value}
                                </dd>
                            </motion.div>
                        ))}

                        {/* The grid's 1px gaps show through as grey blocks when
                            the last row is short, so pad it out. */}
                        {Array.from({ length: (4 - (facts.length % 4)) % 4 }).map((_, index) => (
                            <div key={`filler-${index}`} className="hidden bg-concrete-100 lg:block" />
                        ))}
                    </dl>

                    {project.status === 'ongoing' && (
                        <div className="py-8">
                            <div className="flex items-center justify-between font-mono text-[0.625rem] uppercase tracking-[0.16em] text-concrete-500">
                                <span>Avancement du chantier</span>
                                <span className="text-ink-900">{project.progress}%</span>
                            </div>
                            <Bar
                                className="mt-3 h-1.5 w-full overflow-hidden bg-concrete-200"
                                fillClassName="bg-brand"
                                to={project.progress / 100}
                                duration={1.4}
                                amount={0.1}
                            />
                        </div>
                    )}
                </div>
            </section>

            {/* Narrative */}
            <Section size="lg">
                <div className="shell">
                    {project.body && (
                        <Reveal className="mx-auto max-w-3xl">
                            <div
                                className="prose prose-brand prose-lg max-w-none prose-headings:font-display prose-headings:tracking-[-0.03em]"
                                dangerouslySetInnerHTML={{ __html: project.body }}
                            />
                        </Reveal>
                    )}

                    {narrative.length > 0 && (
                        <div className="mt-14 space-y-12 lg:mt-16 lg:space-y-16">
                            {narrative.map((block, index) => (
                                <motion.div
                                    key={block.key}
                                    initial={{ opacity: 0, y: 36 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, amount: 0.25 }}
                                    transition={{ duration: 0.9 }}
                                    className="grid gap-8 lg:grid-cols-12 lg:gap-16"
                                >
                                    <div className="min-w-0 lg:col-span-4">
                                        <span className="flex size-12 items-center justify-center border border-concrete-300 text-brand-deep">
                                            <Icon name={block.icon} className="size-5" />
                                        </span>
                                        <h2 className="mt-6 text-[clamp(1.5rem,3vw,2.5rem)] text-ink-900">
                                            <SplitText text={t(`projects.${block.key}`)} />
                                        </h2>
                                        <span className="mt-4 block font-mono text-xs tracking-[0.2em] text-concrete-300">
                                            {String(index + 1).padStart(2, '0')}
                                        </span>
                                    </div>

                                    <p className="min-w-0 text-base leading-relaxed text-concrete-600 sm:text-lg lg:col-span-8">
                                        {block.value}
                                    </p>
                                </motion.div>
                            ))}
                        </div>
                    )}
                </div>
            </Section>

            {/* Full-bleed image */}
            {project.cover_image && (
                <ParallaxImage
                    src={project.cover_image}
                    alt={project.title}
                    className="h-[45vh] sm:h-[65vh]"
                    strength={12}
                    zoomable
                />
            )}

            {/* Testimonial */}
            {testimonial && (
                <Section size="lg" tone="muted">
                    <div className="shell">
                        <PullQuote testimonial={testimonial} className="mx-auto max-w-4xl" />
                    </div>
                </Section>
            )}

            {/* Gallery */}
            {project.gallery?.length > 0 && (
                <Section size="lg">
                    <div className="shell">
                        <SectionHeading eyebrow={t('projects.gallery')} title={project.title} />
                        <Gallery images={project.gallery} alt={project.title} className="mt-10" />
                    </div>
                </Section>
            )}

            {/* Services used */}
            {project.services_used?.length > 0 && (
                <Section tone="muted" size="md">
                    <div className="shell">
                        <Reveal>
                            <span className="eyebrow text-concrete-500">{t('projects.services_used')}</span>
                        </Reveal>

                        <div className="mt-8 flex flex-wrap gap-3">
                            {project.services_used.map((slug, index) => (
                                <motion.div
                                    key={slug}
                                    initial={{ opacity: 0, y: 14 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, amount: 0.6 }}
                                    transition={{ duration: 0.6, delay: index * 0.07 }}
                                >
                                    <Link
                                        href={`/metiers/${slug}`}
                                        className="group/tag inline-flex items-center gap-2.5 border border-concrete-300 bg-concrete-50 px-5 py-3 font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-ink-900 transition-colors duration-400 hover:border-ink-900 hover:bg-ink-900 hover:text-concrete-50"
                                    >
                                        {slug.replaceAll('-', ' ')}
                                        <Icon
                                            name="arrow-up-right"
                                            className="size-3.5 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/tag:translate-x-0.5 group-hover/tag:-translate-y-0.5"
                                            strokeWidth={2}
                                        />
                                    </Link>
                                </motion.div>
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            {/* Similar */}
            {similar.length > 0 && (
                <Section size="lg">
                    <div className="shell">
                        <SectionHeading
                            align="between"
                            eyebrow={t('projects.similar')}
                            title={t('home.projects_title')}
                            action={
                                <Button href="/realisations" tone="outline">
                                    {t('common.view_all_projects')}
                                </Button>
                            }
                        />

                        <div className="mt-10 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                            {similar.map((item, index) => (
                                <ProjectCard key={item.id} project={item} index={index} />
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            {/* Next project */}
            {nextProject && (
                <Link
                    href={`/realisations/${nextProject.slug}`}
                    className="group/next noise-overlay relative block overflow-hidden bg-ink-950 py-16 text-concrete-50 sm:py-24"
                >
                    {nextProject.cover_image && (
                        <img
                            src={nextProject.cover_image}
                            alt=""
                            loading="lazy"
                            className="absolute inset-0 h-full w-full scale-105 object-cover opacity-25 transition-transform duration-[1.6s] [transition-timing-function:var(--ease-out-expo)] group-hover/next:scale-110 group-hover/next:opacity-35"
                        />
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/70 to-ink-950/40" />

                    <div className="shell relative flex flex-col items-start gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <span className="eyebrow text-brand">{t('projects.next_project')}</span>
                            <h2 className="mt-5 max-w-3xl text-[clamp(2rem,5.5vw,4.5rem)] leading-[0.92]">
                                {nextProject.title}
                            </h2>
                            <p className="mt-4 flex items-center gap-2 font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-concrete-400">
                                <Icon name="map-pin" className="size-3.5" />
                                {nextProject.location}
                            </p>
                        </div>

                        <span className="flex size-20 shrink-0 items-center justify-center rounded-full border border-concrete-50/25 transition-all duration-700 [transition-timing-function:var(--ease-out-expo)] group-hover/next:border-brand group-hover/next:bg-brand group-hover/next:text-ink-950">
                            <Icon name="arrow-right" className="size-7" strokeWidth={1.6} />
                        </span>
                    </div>
                </Link>
            )}

            <CtaBand />
        </>
    );
}
