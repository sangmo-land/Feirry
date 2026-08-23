import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { EASE } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { Button, Icon, Section, SectionHeading } from '@/Components/Ui';
import { Accordion } from '@/Components/Accordion';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb } from '@/Sections/Shared';

export default function ServicesIndex({ services = [], faqs = [] }) {
    const { t } = useSite();

    return (
        <>
            <PageHero
                title={t('services.hero_title')}
                lead={t('services.hero_lead')}
                image="/storage/heroes/services.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.services')}</Crumb>
                    </>
                }
            />

            {/* Index list — each row expands into an editorial block */}
            <Section size="lg">
                <div className="shell">
                    <div className="divide-y divide-concrete-200 border-y border-concrete-200">
                        {services.map((service, index) => (
                            <ServiceRow key={service.id} service={service} index={index} t={t} />
                        ))}
                    </div>
                </div>
            </Section>

            {faqs.length > 0 && (
                <Section tone="muted" size="lg">
                    <div className="shell grid gap-14 lg:grid-cols-12 lg:gap-20">
                        <div className="min-w-0 lg:col-span-4">
                            <div className="lg:sticky lg:top-32">
                                <SectionHeading eyebrow={t('faq.title')} title={t('faq.hero_title')} />
                                <Reveal delay={0.2} className="mt-8">
                                    <Button href="/faq" tone="outline">
                                        {t('common.view_all')}
                                    </Button>
                                </Reveal>
                            </div>
                        </div>
                        <div className="min-w-0 lg:col-span-8">
                            <Accordion items={faqs} defaultOpen={0} />
                        </div>
                    </div>
                </Section>
            )}

            <CtaBand
                title={t('services.ask_quote')}
                primary={{ href: '/devis', label: t('nav.quote') }}
            />
        </>
    );
}

function ServiceRow({ service, index, t }) {
    const reverse = index % 2 === 1;

    return (
        <motion.article
            initial={{ opacity: 0, y: 40 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.15 }}
            transition={{ duration: 0.9, ease: EASE.outExpo }}
            className="group/row py-10 sm:py-14"
        >
            <div className="grid gap-10 lg:grid-cols-12 lg:gap-16">
                {/* Number + icon */}
                <div className="min-w-0 flex items-start gap-5 lg:col-span-2">
                    <span className="font-mono text-xs tracking-[0.2em] text-concrete-400">
                        {String(index + 1).padStart(2, '0')}
                    </span>
                    <span className="flex size-12 items-center justify-center border border-concrete-300 text-ink-900 transition-colors duration-600 group-hover/row:border-brand group-hover/row:bg-brand">
                        <Icon name={service.icon} className="size-5" />
                    </span>
                </div>

                {/* Copy */}
                <div className={reverse ? 'lg:order-3 lg:col-span-5' : 'lg:col-span-5'}>
                    <h2 className="text-[clamp(1.6rem,3.4vw,2.75rem)] text-ink-900">
                        <Link href={`/metiers/${service.slug}`} className="link-draw">
                            {service.title}
                        </Link>
                    </h2>

                    {service.tagline && (
                        <p className="mt-2 font-mono text-[0.6875rem] uppercase tracking-[0.16em] text-brand-deep">
                            {service.tagline}
                        </p>
                    )}

                    <p className="mt-6 text-sm leading-relaxed text-concrete-600 sm:text-base">
                        {service.excerpt}
                    </p>

                    {service.features?.length > 0 && (
                        <ul className="mt-8 grid gap-2 sm:grid-cols-2">
                            {service.features.slice(0, 6).map((feature) => (
                                <li key={feature} className="flex items-start gap-2.5 text-sm text-concrete-700">
                                    <Icon name="check" className="mt-0.5 size-4 shrink-0 text-brand-deep" strokeWidth={2.4} />
                                    {feature}
                                </li>
                            ))}
                        </ul>
                    )}

                    <div className="mt-9 flex flex-wrap items-center gap-6">
                        <Button href={`/metiers/${service.slug}`} tone="outline" size="sm">
                            {t('common.read_more')}
                        </Button>
                        {service.starting_price && (
                            <span className="font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-concrete-500">
                                {t('services.starting_from')}{' '}
                                <span className="text-ink-900">{service.starting_price}</span>
                            </span>
                        )}
                    </div>
                </div>

                {/* Image */}
                <div className={reverse ? 'lg:order-2 lg:col-span-5' : 'lg:col-span-5'}>
                    <Link
                        href={`/metiers/${service.slug}`}
                        className="block aspect-[16/11] overflow-hidden bg-ink-850"
                    >
                        {service.cover_image ? (
                            <img
                                src={service.cover_image}
                                alt={service.title}
                                loading="lazy"
                                className="h-full w-full object-cover transition-transform duration-[1.3s] [transition-timing-function:var(--ease-out-expo)] group-hover/row:scale-105"
                            />
                        ) : (
                            <div className="blueprint-grid h-full w-full" />
                        )}
                    </Link>
                </div>
            </div>
        </motion.article>
    );
}
