import { motion } from 'motion/react';
import { useSite } from '@/Hooks';
import { Button, Icon, Section, SectionHeading } from '@/Components/Ui';
import { Accordion } from '@/Components/Accordion';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb, ProcessTimeline } from '@/Sections/Shared';

export default function Process({ steps = [], faqs = [] }) {
    const { t } = useSite();

    return (
        <>
            <PageHero
                title={t('process.hero_title')}
                lead={t('process.hero_lead')}
                image="/storage/heroes/process.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.process')}</Crumb>
                    </>
                }
            />

            {/* Step index rail */}
            <section className="border-b border-concrete-200 bg-concrete-100 py-8">
                <div className="shell">
                    <div className="grid gap-px bg-concrete-200 sm:grid-cols-3 lg:grid-cols-5">
                        {steps.map((step, index) => (
                            <motion.a
                                key={step.id}
                                href={`#etape-${index + 1}`}
                                initial={{ opacity: 0, y: 16 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, amount: 0.5 }}
                                transition={{ duration: 0.6, delay: index * 0.06 }}
                                className="group/step flex items-center gap-4 bg-concrete-100 p-5 transition-colors duration-400 hover:bg-concrete-50"
                            >
                                <span className="flex size-9 shrink-0 items-center justify-center rounded-full border border-concrete-300 font-mono text-[0.6875rem] font-semibold text-ink-900 transition-colors duration-400 group-hover/step:border-brand group-hover/step:bg-brand">
                                    {index + 1}
                                </span>
                                <span className="min-w-0 font-display text-sm font-bold leading-tight tracking-[-0.02em] text-ink-900">
                                    {step.title}
                                </span>
                            </motion.a>
                        ))}
                    </div>
                </div>
            </section>

            {/* Detailed steps */}
            <Section size="lg">
                <div className="shell">
                    <div className="space-y-16 lg:space-y-24">
                        {steps.map((step, index) => (
                            <article key={step.id} id={`etape-${index + 1}`} className="scroll-mt-32">
                                <div className="grid gap-10 lg:grid-cols-12 lg:gap-16">
                                    <div className="min-w-0 lg:col-span-5">
                                        <div className="lg:sticky lg:top-32">
                                            <Reveal y={20}>
                                                <span className="font-display text-[clamp(4rem,12vw,9rem)] font-black leading-[0.8] tracking-[-0.06em] text-concrete-200">
                                                    {String(index + 1).padStart(2, '0')}
                                                </span>
                                            </Reveal>

                                            <Reveal y={20} delay={0.1}>
                                                <span className="mt-4 flex size-14 items-center justify-center border border-concrete-300 text-ink-900">
                                                    <Icon name={step.icon} className="size-6" />
                                                </span>
                                            </Reveal>

                                            <Reveal y={20} delay={0.15}>
                                                <h2 className="mt-7 text-[clamp(1.6rem,3.4vw,2.75rem)] text-ink-900">
                                                    {step.title}
                                                </h2>
                                            </Reveal>

                                            {step.duration && (
                                                <Reveal y={16} delay={0.2}>
                                                    <p className="mt-4 inline-flex items-center gap-2 border border-brand/35 bg-brand/10 px-4 py-2 font-mono text-[0.625rem] uppercase tracking-[0.16em] text-brand-deep">
                                                        <Icon name="clock" className="size-3.5" />
                                                        {t('process.duration')} : {step.duration}
                                                    </p>
                                                </Reveal>
                                            )}
                                        </div>
                                    </div>

                                    <div className="min-w-0 lg:col-span-7">
                                        <Reveal y={26} delay={0.1}>
                                            <p className="text-base leading-relaxed text-concrete-600 sm:text-lg">
                                                {step.description}
                                            </p>
                                        </Reveal>

                                        {step.deliverables?.length > 0 && (
                                            <Reveal y={26} delay={0.18} className="mt-12">
                                                <span className="eyebrow text-concrete-400">
                                                    {t('process.deliverables')}
                                                </span>

                                                <ul className="mt-6 divide-y divide-concrete-200 border-y border-concrete-200">
                                                    {step.deliverables.map((item, deliverableIndex) => (
                                                        <motion.li
                                                            key={item}
                                                            initial={{ opacity: 0, x: -14 }}
                                                            whileInView={{ opacity: 1, x: 0 }}
                                                            viewport={{ once: true, amount: 0.6 }}
                                                            transition={{ duration: 0.6, delay: deliverableIndex * 0.06 }}
                                                            className="flex items-center gap-4 py-4"
                                                        >
                                                            <Icon
                                                                name="check"
                                                                className="size-4 shrink-0 text-brand-deep"
                                                                strokeWidth={2.5}
                                                            />
                                                            <span className="text-sm text-concrete-700">{item}</span>
                                                        </motion.li>
                                                    ))}
                                                </ul>
                                            </Reveal>
                                        )}
                                    </div>
                                </div>
                            </article>
                        ))}
                    </div>
                </div>
            </Section>

            {/* Compact recap */}
            <Section tone="muted" size="lg">
                <div className="shell">
                    <SectionHeading
                        eyebrow={t('home.process_eyebrow')}
                        title="Le déroulé en un coup d'oeil"
                        lead="Du premier appel à la garantie de parfait achèvement, voici la séquence complète."
                    />
                    <div className="mt-14">
                        <ProcessTimeline steps={steps} compact />
                    </div>
                </div>
            </Section>

            {faqs.length > 0 && (
                <Section size="lg">
                    <div className="shell grid gap-14 lg:grid-cols-12 lg:gap-20">
                        <div className="min-w-0 lg:col-span-4">
                            <div className="lg:sticky lg:top-32">
                                <SectionHeading eyebrow={t('faq.title')} title={t('faq.still_questions')} />
                                <Reveal delay={0.2} className="mt-8">
                                    <Button href="/contact" tone="outline">
                                        {t('faq.contact_us')}
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

            <CtaBand />
        </>
    );
}
