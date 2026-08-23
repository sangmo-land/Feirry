import { useMemo, useState } from 'react';
import { cn } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { Button, Icon, Section, SectionHeading } from '@/Components/Ui';
import { JobCard } from '@/Components/Cards';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb, SplitFeature } from '@/Sections/Shared';

export default function CareersIndex({ openings = [], departments = [] }) {
    const { t, site } = useSite();
    const [department, setDepartment] = useState('all');

    const visible = useMemo(
        () => (department === 'all' ? openings : openings.filter((job) => job.department === department)),
        [openings, department],
    );

    return (
        <>
            <PageHero
                title={t('careers.hero_title')}
                lead={t('careers.hero_lead')}
                image="/storage/heroes/careers.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.careers')}</Crumb>
                    </>
                }
            >
                <div className="flex flex-wrap items-center gap-4">
                    <Button href="#offres" tone="brand" icon="arrow-down">
                        {t('careers.open_positions')} ({openings.length})
                    </Button>
                    <Button
                        href={`mailto:${site.contact?.email_rh}`}
                        tone="ghost"
                        icon="mail"
                    >
                        {t('careers.spontaneous')}
                    </Button>
                </div>
            </PageHero>

            {/* Why join */}
            <Section size="lg">
                <div className="shell">
                    <SplitFeature
                        eyebrow={t('careers.why_join')}
                        title="Des chantiers exigeants, une entreprise qui tient ses engagements"
                        image="/storage/sections/careers-why-join.jpg"
                        imageAlt="Équipe FEIRRY sur un chantier"
                        stat={{ value: 340, suffix: '', label: 'Collaborateurs en CDI' }}
                    >
                        <p>
                            Sur 385 collaborateurs, 340 sont en contrat à durée indéterminée. Nous préférons
                            former durablement plutôt que recruter au coup par coup, parce qu'un ouvrage que
                            l'on garantit dix ans se construit avec des gens qui connaissent nos méthodes.
                        </p>
                        <ul className="space-y-3 pt-2">
                            {[
                                'Salaires versés le 28 de chaque mois, sans exception',
                                'Assurance santé familiale prise en charge',
                                'Plan de formation individualisé et certifications financées',
                                'Équipements de protection fournis et renouvelés',
                                'Prime de performance indexée sur les résultats du chantier',
                            ].map((item) => (
                                <li key={item} className="flex items-start gap-3 text-sm text-concrete-700">
                                    <Icon name="check" className="mt-0.5 size-4 shrink-0 text-brand-deep" strokeWidth={2.4} />
                                    {item}
                                </li>
                            ))}
                        </ul>
                    </SplitFeature>
                </div>
            </Section>

            {/* Openings */}
            <Section tone="muted" size="lg" id="offres">
                <div className="shell">
                    <SectionHeading
                        eyebrow={t('careers.open_positions')}
                        title={`${openings.length} poste${openings.length > 1 ? 's' : ''} ouvert${openings.length > 1 ? 's' : ''}`}
                        lead="Chaque candidature reçoit une réponse, retenue ou non, sous quinze jours."
                    />

                    {departments.length > 1 && (
                        <Reveal className="mt-10 flex flex-wrap gap-2">
                            {['all', ...departments].map((item) => (
                                <button
                                    key={item}
                                    type="button"
                                    onClick={() => setDepartment(item)}
                                    className={cn(
                                        'whitespace-nowrap rounded-full border px-4 py-2 font-mono text-[0.625rem] font-semibold uppercase tracking-[0.12em] transition-all duration-400',
                                        department === item
                                            ? 'border-ink-900 bg-ink-900 text-concrete-50'
                                            : 'border-concrete-300 text-concrete-600 hover:border-ink-900 hover:text-ink-900',
                                    )}
                                >
                                    {item === 'all' ? t('common.all') : item}
                                </button>
                            ))}
                        </Reveal>
                    )}

                    {visible.length === 0 ? (
                        <div className="mt-10 border border-dashed border-concrete-300 py-16 text-center">
                            <Icon name="briefcase" className="mx-auto size-8 text-concrete-300" />
                            <p className="mt-5 text-sm text-concrete-500">{t('careers.no_positions')}</p>
                            <Button
                                href={`mailto:${site.contact?.email_rh}`}
                                tone="outline"
                                size="sm"
                                className="mt-8"
                            >
                                {t('careers.spontaneous')}
                            </Button>
                        </div>
                    ) : (
                        <div className="mt-12 border-t border-concrete-200">
                            {visible.map((job, index) => (
                                <JobCard key={job.id} job={job} index={index} />
                            ))}
                        </div>
                    )}
                </div>
            </Section>

            {/* Spontaneous */}
            <Section size="md">
                <div className="shell">
                    <div className="border border-concrete-200 bg-concrete-100 p-7 sm:p-12 lg:p-16">
                        <div className="grid gap-10 lg:grid-cols-12 lg:items-center lg:gap-16">
                            <div className="min-w-0 lg:col-span-8">
                                <span className="eyebrow text-concrete-500">{t('careers.spontaneous')}</span>
                                <h2 className="mt-5 text-[clamp(1.6rem,3.4vw,2.75rem)] text-ink-900">
                                    Aucune offre ne correspond à votre profil ?
                                </h2>
                                <p className="mt-5 max-w-2xl text-sm leading-relaxed text-concrete-600 sm:text-base">
                                    Envoyez-nous votre CV. Nous conservons les candidatures pendant douze mois
                                    et nous recontactons systématiquement les profils techniques lorsqu'un
                                    poste s'ouvre.
                                </p>
                            </div>

                            <div className="min-w-0 lg:col-span-4 lg:text-right">
                                <Button href={`mailto:${site.contact?.email_rh}`} tone="solid" icon="mail">
                                    {t('careers.spontaneous')}
                                </Button>
                                <a
                                    href={`mailto:${site.contact?.email_rh}`}
                                    className="mt-4 block break-all font-mono text-[0.6875rem] uppercase tracking-[0.12em] text-concrete-500 transition-colors hover:text-ink-900"
                                >
                                    {site.contact?.email_rh}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </Section>

            <CtaBand
                title="Une question sur nos recrutements ?"
                lead="Notre responsable RH répond directement aux candidats."
                primary={{ href: `mailto:${site.contact?.email_rh}`, label: t('common.write_us') }}
                secondary={{ href: '/equipe', label: t('team.title') }}
            />
        </>
    );
}
