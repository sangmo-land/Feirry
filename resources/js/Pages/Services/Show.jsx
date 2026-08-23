import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { useSite } from '@/Hooks';
import { Button, Icon, Section, SectionHeading } from '@/Components/Ui';
import { ProjectCard } from '@/Components/Cards';
import { Gallery } from '@/Components/Media';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb } from '@/Sections/Shared';

export default function ServiceShow({ service, relatedProjects = [], otherServices = [] }) {
    const { t } = useSite();

    return (
        <>
            <PageHero
                title={service.title}
                lead={service.excerpt}
                image={service.cover_image}
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb href="/metiers">{t('nav.services')}</Crumb>
                        <Crumb last>{service.title}</Crumb>
                    </>
                }
            >
                <div className="flex flex-wrap items-center gap-6">
                    <Button href="/devis" tone="brand">
                        {t('services.ask_quote')}
                    </Button>
                    {service.starting_price && (
                        <span className="font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-concrete-400">
                            {t('services.starting_from')}{' '}
                            <span className="text-concrete-50">{service.starting_price}</span>
                        </span>
                    )}
                </div>
            </PageHero>

            <Section size="lg">
                <div className="shell grid gap-16 lg:grid-cols-12 lg:gap-20">
                    {/* Body */}
                    <div className="min-w-0 lg:col-span-7">
                        {service.body && (
                            <Reveal>
                                <div
                                    className="prose prose-brand max-w-none prose-headings:font-display prose-headings:tracking-[-0.03em] prose-h3:mt-12 prose-h3:text-2xl prose-p:leading-relaxed"
                                    dangerouslySetInnerHTML={{ __html: service.body }}
                                />
                            </Reveal>
                        )}

                        {service.gallery?.length > 0 && (
                            <div className="mt-12">
                                <Reveal>
                                    <h2 className="text-2xl text-ink-900">{t('projects.gallery')}</h2>
                                </Reveal>
                                <Gallery images={service.gallery} alt={service.title} className="mt-8" />
                            </div>
                        )}
                    </div>

                    {/* Sidebar */}
                    <aside className="min-w-0 lg:col-span-5">
                        <div className="lg:sticky lg:top-32">
                            {service.features?.length > 0 && (
                                <Reveal>
                                    <div className="border border-concrete-200 bg-concrete-100 p-8 sm:p-10">
                                        <span className="eyebrow text-concrete-500">{t('services.included')}</span>

                                        <ul className="mt-7 space-y-3.5">
                                            {service.features.map((feature, index) => (
                                                <motion.li
                                                    key={feature}
                                                    initial={{ opacity: 0, x: -12 }}
                                                    whileInView={{ opacity: 1, x: 0 }}
                                                    viewport={{ once: true, amount: 0.5 }}
                                                    transition={{ duration: 0.55, delay: index * 0.05 }}
                                                    className="flex items-start gap-3 text-sm text-concrete-700"
                                                >
                                                    <Icon
                                                        name="check"
                                                        className="mt-0.5 size-4 shrink-0 text-brand-deep"
                                                        strokeWidth={2.4}
                                                    />
                                                    {feature}
                                                </motion.li>
                                            ))}
                                        </ul>

                                        <div className="mt-9 border-t border-concrete-200 pt-8">
                                            <Button href="/devis" tone="solid" className="w-full">
                                                {t('nav.quote')}
                                            </Button>
                                        </div>
                                    </div>
                                </Reveal>
                            )}

                            {otherServices.length > 0 && (
                                <Reveal delay={0.15} className="mt-10">
                                    <span className="eyebrow text-concrete-500">
                                        {t('common.view_all_services')}
                                    </span>

                                    <ul className="mt-6 divide-y divide-concrete-200 border-y border-concrete-200">
                                        {otherServices.map((other) => (
                                            <li key={other.id}>
                                                <Link
                                                    href={`/metiers/${other.slug}`}
                                                    className="group/other flex items-center justify-between gap-4 py-4"
                                                >
                                                    <span className="flex items-center gap-3.5">
                                                        <Icon
                                                            name={other.icon}
                                                            className="size-4 shrink-0 text-concrete-400 transition-colors duration-400 group-hover/other:text-brand-deep"
                                                        />
                                                        <span className="font-display text-sm font-bold tracking-[-0.02em] text-ink-900">
                                                            {other.title}
                                                        </span>
                                                    </span>
                                                    <Icon
                                                        name="arrow-right"
                                                        className="size-4 shrink-0 text-concrete-300 transition-all duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/other:translate-x-1 group-hover/other:text-ink-900"
                                                        strokeWidth={2}
                                                    />
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </Reveal>
                            )}
                        </div>
                    </aside>
                </div>
            </Section>

            {relatedProjects.length > 0 && (
                <Section tone="muted" size="lg">
                    <div className="shell">
                        <SectionHeading
                            align="between"
                            eyebrow={t('services.related_projects')}
                            title={t('home.projects_title')}
                            action={
                                <Button href="/realisations" tone="outline">
                                    {t('common.view_all_projects')}
                                </Button>
                            }
                        />

                        <div className="mt-10 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                            {relatedProjects.map((project, index) => (
                                <ProjectCard key={project.id} project={project} index={index} />
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            <CtaBand />
        </>
    );
}
