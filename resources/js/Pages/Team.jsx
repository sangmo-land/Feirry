import { useSite } from '@/Hooks';
import { Button, Icon, Section, SectionHeading } from '@/Components/Ui';
import { TeamCard } from '@/Components/Cards';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { CtaBand, Crumb, StatsBand } from '@/Sections/Shared';

export default function Team({ leadership = [], members = [] }) {
    const { t } = useSite();

    return (
        <>
            <PageHero
                title={t('team.hero_title')}
                lead={t('team.hero_lead')}
                image="/storage/heroes/team.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.team')}</Crumb>
                    </>
                }
            >
                <Button href="/carrieres" tone="brand">
                    {t('team.join_us')}
                </Button>
            </PageHero>

            {leadership.length > 0 && (
                <Section size="lg">
                    <div className="shell">
                        <SectionHeading
                            eyebrow={t('team.leadership')}
                            title="La Direction"
                            lead="Quatre responsables qui suivent personnellement les opérations engageantes, et que vous rencontrez avant de signer."
                        />

                        <div className="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                            {leadership.map((member, index) => (
                                <TeamCard key={member.id} member={member} index={index} />
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            {members.length > 0 && (
                <Section tone="muted" size="lg">
                    <div className="shell">
                        <SectionHeading
                            eyebrow={t('team.experts')}
                            title="L'encadrement technique"
                            lead="Les personnes qui tiennent le chantier au quotidien : bureau d'études, conduite de travaux, QHSE et logistique."
                        />

                        <div className="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                            {members.map((member, index) => (
                                <TeamCard key={member.id} member={member} index={index} />
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            {/* Culture */}
            <Section size="lg">
                <div className="shell">
                    <div className="grid gap-14 lg:grid-cols-12 lg:gap-20">
                        <div className="min-w-0 lg:col-span-5">
                            <SectionHeading eyebrow={t('careers.why_join')} title="Une équipe formée ici" />
                        </div>

                        <div className="min-w-0 lg:col-span-7">
                            <div className="space-y-5 text-base leading-relaxed text-concrete-600">
                                <p>
                                    Sur 385 collaborateurs, 340 sont en contrat à durée indéterminée. Ce n'est
                                    pas la norme du secteur au Cameroun, où le recours massif au journalier est
                                    courant. C'est un choix : un compagnon qui connaît nos méthodes et nos
                                    exigences produit un ouvrage que nous pouvons garantir dix ans.
                                </p>
                                <p>
                                    Nous formons chaque année entre dix-huit et vingt-quatre apprentis en
                                    alternance sur nos chantiers. Six ans après le lancement du programme, 61 %
                                    d'entre eux travaillent toujours chez nous.
                                </p>
                            </div>

                            <div className="mt-12 grid gap-px bg-concrete-200 sm:grid-cols-2">
                                {[
                                    { icon: 'users', value: '340', label: 'Collaborateurs en CDI' },
                                    { icon: 'shield-check', value: '0', label: 'Accident grave depuis 2019' },
                                    { icon: 'trending-up', value: '61 %', label: "d'apprentis embauchés" },
                                    { icon: 'badge-check', value: '20', label: 'Apprentis formés par an' },
                                ].map((item, index) => (
                                    <Reveal key={item.label} delay={index * 0.07}>
                                        <div className="bg-concrete-50 p-7">
                                            <Icon name={item.icon} className="size-5 text-brand-deep" />
                                            <span className="mt-5 block font-display text-3xl font-black tracking-tight text-ink-900">
                                                {item.value}
                                            </span>
                                            <span className="mt-1.5 block font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-500">
                                                {item.label}
                                            </span>
                                        </div>
                                    </Reveal>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="mt-16">
                    <StatsBand />
                </div>
            </Section>

            <CtaBand
                title={t('team.join_us')}
                lead="Nous recrutons toute l'année des profils techniques et administratifs, à Douala comme sur nos chantiers en région."
                primary={{ href: '/carrieres', label: t('careers.open_positions') }}
                secondary={{ href: '/contact', label: t('careers.spontaneous') }}
            />
        </>
    );
}
