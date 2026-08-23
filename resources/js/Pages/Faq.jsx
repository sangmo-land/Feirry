import { useState } from 'react';
import { motion } from 'motion/react';
import { cn } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { Button, Section, SectionHeading } from '@/Components/Ui';
import { Accordion } from '@/Components/Accordion';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb } from '@/Sections/Shared';

const GROUP_LABELS = {
    general: { fr: 'Généralités', en: 'General' },
    services: { fr: 'Prestations', en: 'Services' },
    process: { fr: 'Déroulé du chantier', en: 'How a project runs' },
    quote: { fr: 'Devis & paiement', en: 'Quotes & payment' },
};

export default function Faq({ groups = [] }) {
    const { t, locale } = useSite();
    const [active, setActive] = useState('all');

    const visible = active === 'all' ? groups : groups.filter((group) => group.key === active);

    return (
        <>
            <PageHero
                title={t('faq.hero_title')}
                lead={t('faq.hero_lead')}
                image="/storage/heroes/faq.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.faq')}</Crumb>
                    </>
                }
            />

            <Section size="lg">
                <div className="shell">
                    {/* Filters */}
                    <Reveal>
                        <div className="flex flex-wrap gap-2">
                            {[{ key: 'all' }, ...groups].map((group) => {
                                const label =
                                    group.key === 'all'
                                        ? t('common.all')
                                        : (GROUP_LABELS[group.key]?.[locale] ?? group.key);

                                return (
                                    <button
                                        key={group.key}
                                        type="button"
                                        onClick={() => setActive(group.key)}
                                        className={cn(
                                            'whitespace-nowrap rounded-full border px-5 py-2.5 font-mono text-[0.6875rem] font-semibold uppercase tracking-[0.14em] transition-all duration-400',
                                            active === group.key
                                                ? 'border-ink-900 bg-ink-900 text-concrete-50'
                                                : 'border-concrete-300 text-concrete-600 hover:border-ink-900 hover:text-ink-900',
                                        )}
                                    >
                                        {label}
                                    </button>
                                );
                            })}
                        </div>
                    </Reveal>

                    <div className="mt-10 space-y-14">
                        {visible.map((group) => (
                            <motion.section
                                key={group.key}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.6 }}
                            >
                                <h2 className="text-[clamp(1.5rem,3vw,2.25rem)] text-ink-900">
                                    {GROUP_LABELS[group.key]?.[locale] ?? group.key}
                                </h2>
                                <Accordion items={group.items} className="mt-8" defaultOpen={0} />
                            </motion.section>
                        ))}
                    </div>
                </div>
            </Section>

            <Section tone="muted" size="md">
                <div className="shell">
                    <SectionHeading
                        align="center"
                        eyebrow={t('faq.still_questions')}
                        title="Posez-nous la vôtre"
                        lead="Un ingénieur vous rappelle sous 48 heures ouvrées. Sans engagement, et sans relance commerciale."
                    />

                    <Reveal delay={0.2} className="mt-10 flex flex-wrap justify-center gap-4">
                        <Button href="/contact" tone="solid">
                            {t('faq.contact_us')}
                        </Button>
                        <Button href="/devis" tone="outline">
                            {t('nav.quote')}
                        </Button>
                    </Reveal>
                </div>
            </Section>

            <CtaBand />
        </>
    );
}
