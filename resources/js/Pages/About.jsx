import { useRef } from 'react';
import { motion, useScroll, useTransform } from 'motion/react';
import { useReducedMotion, useSite } from '@/Hooks';
import { Button, Icon, Section, SectionHeading } from '@/Components/Ui';
import { TeamCard } from '@/Components/Cards';
import { OfficeMap } from '@/Components/Map';
import { PageHero } from '@/Sections/Hero';
import { Reveal } from '@/Components/Motion';
import { CtaBand, Crumb, PartnerStrip, SplitFeature, StatsBand, ValuesGrid } from '@/Sections/Shared';

export default function About({ milestones = [], certifications = [], leadership = [], partners = [], projectCount }) {
    const { t, site, locationAnchor } = useSite();

    return (
        <>
            <PageHero
                title={t('about.hero_title')}
                lead={t('about.hero_lead')}
                image="/storage/heroes/about.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.about')}</Crumb>
                    </>
                }
            />

            {/* ---------------------------------------------------------- */}
            {/* Story                                                       */}
            {/* ---------------------------------------------------------- */}
            <Section size="lg">
                <div className="shell">
                    <SplitFeature
                        eyebrow={t('about.story_eyebrow')}
                        title={t('about.story_title')}
                        image="/storage/sections/about-story.jpg"
                        imageAlt="Réunion de chantier chez FEIRRY"
                        stat={{ value: projectCount ?? 240, suffix: '+', label: 'Ouvrages livrés' }}
                        action={
                            <Button href="/realisations" tone="outline">
                                {t('common.view_all_projects')}
                            </Button>
                        }
                    >
                        <p>
                            En 2009, trois ingénieurs quittent leurs postes salariés pour ouvrir un bureau de
                            deux pièces à Akwa. Le constat de départ est simple : à Douala, l'écart entre ce qui
                            est promis au client et ce qui lui est effectivement livré est devenu la norme du
                            secteur, et personne n'y gagne durablement.
                        </p>
                        <p>
                            Seize ans plus tard, l'entreprise emploie 385 personnes, dispose d'un bureau
                            d'études intégré, d'un parc de 42 engins et de trois implantations. Le principe
                            fondateur n'a pas bougé : ce qui est chiffré est ce qui est construit, et ce qui est
                            construit est vérifié.
                        </p>
                        <p>
                            Nous restons une entreprise camerounaise, à capitaux camerounais, qui forme ses
                            compagnons sur ses propres chantiers. C'est une position économique autant qu'une
                            conviction : les compétences qui restent au pays sont celles qui reviennent sur le
                            chantier suivant.
                        </p>
                    </SplitFeature>
                </div>

                <div className="mt-16 sm:mt-20">
                    <StatsBand />
                </div>
            </Section>

            {/* ---------------------------------------------------------- */}
            {/* Values                                                      */}
            {/* ---------------------------------------------------------- */}
            <Section tone="muted" size="lg">
                <div className="shell">
                    <SectionHeading
                        eyebrow={t('about.values_eyebrow')}
                        title={t('about.values_title')}
                        lead="Ces quatre principes ne sont pas décoratifs : chacun se traduit par une clause contractuelle ou une procédure vérifiable."
                    />
                    <div className="mt-12">
                        <ValuesGrid />
                    </div>
                </div>
            </Section>

            {/* ---------------------------------------------------------- */}
            {/* Timeline                                                    */}
            {/* ---------------------------------------------------------- */}
            {milestones.length > 0 && <Timeline milestones={milestones} />}

            {/* ---------------------------------------------------------- */}
            {/* HSE                                                         */}
            {/* ---------------------------------------------------------- */}
            <Section size="lg" className="bg-concrete-50">
                <div className="shell">
                    <SplitFeature
                        reverse
                        eyebrow={t('about.hse_eyebrow')}
                        title={t('about.hse_title')}
                        image="/storage/sections/about-hse.jpg"
                        imageAlt="Équipe de chantier équipée de EPI"
                        stat={{ value: 0, suffix: '', label: 'Accident grave depuis 2019' }}
                    >
                        <p>{t('about.hse_lead')}</p>
                        <ul className="space-y-3 pt-2">
                            {[
                                'Plan particulier de sécurité rédigé avant chaque ouverture de chantier',
                                'Quart d\'heure sécurité quotidien, animé par le chef de chantier',
                                'Audit interne bimensuel par la Responsable QHSE',
                                'Conducteurs de travaux formés aux premiers secours',
                                'Contrôle quotidien du port des EPI, consigné au registre',
                            ].map((item) => (
                                <li key={item} className="flex items-start gap-3 text-sm text-concrete-700">
                                    <Icon name="shield-check" className="mt-0.5 size-4 shrink-0 text-jade" strokeWidth={2} />
                                    {item}
                                </li>
                            ))}
                        </ul>
                    </SplitFeature>
                </div>
            </Section>

            {/* ---------------------------------------------------------- */}
            {/* Equipment                                                   */}
            {/* ---------------------------------------------------------- */}
            <Section tone="dark" size="lg" className="noise-overlay overflow-hidden">
                <div className="blueprint-grid absolute inset-0 opacity-50" />
                <div className="shell relative">
                    <SectionHeading
                        tone="light"
                        eyebrow={t('about.equipment_eyebrow')}
                        title={t('about.equipment_title')}
                        lead="Détenir son matériel plutôt que le louer coûte plus cher à l'achat et beaucoup moins cher en jours d'attente. C'est le calcul que nous avons fait en 2018, et il tient toujours."
                    />

                    <div className="mt-12 grid gap-px bg-ink-800 sm:grid-cols-2 lg:grid-cols-4">
                        {[
                            { icon: 'factory', value: '42', label: 'Engins et véhicules en propre' },
                            { icon: 'gauge', value: '94 %', label: 'Taux de disponibilité du parc' },
                            { icon: 'wrench', value: '1', label: 'Atelier de maintenance intégré' },
                            { icon: 'route', value: '3', label: 'Bases : Douala, Yaoundé, Kribi' },
                        ].map((item, index) => (
                            <motion.div
                                key={item.label}
                                initial={{ opacity: 0, y: 26 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, amount: 0.3 }}
                                transition={{ duration: 0.8, delay: index * 0.08 }}
                                className="bg-ink-900 p-8 sm:p-10"
                            >
                                <Icon name={item.icon} className="size-6 text-brand" />
                                <span className="mt-6 block font-display text-4xl font-black tracking-tight text-concrete-50">
                                    {item.value}
                                </span>
                                <span className="mt-2 block font-mono text-[0.625rem] uppercase leading-relaxed tracking-[0.14em] text-concrete-500">
                                    {item.label}
                                </span>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </Section>

            {/* ---------------------------------------------------------- */}
            {/* Certifications                                              */}
            {/* ---------------------------------------------------------- */}
            {certifications.length > 0 && (
                <Section size="lg">
                    <div className="shell">
                        <SectionHeading
                            eyebrow={t('about.certifications_eyebrow')}
                            title={t('about.certifications_title')}
                            lead="Chaque attestation en cours de validité est remise au maître d'ouvrage à la signature du marché."
                        />

                        <div className="mt-12 divide-y divide-concrete-200 border-y border-concrete-200">
                            {certifications.map((certification, index) => (
                                <motion.div
                                    key={certification.id}
                                    initial={{ opacity: 0, y: 20 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, amount: 0.4 }}
                                    transition={{ duration: 0.7, delay: index * 0.06 }}
                                    className="grid gap-4 py-8 lg:grid-cols-12 lg:gap-10"
                                >
                                    <div className="min-w-0 flex items-start gap-4 lg:col-span-4">
                                        <span className="flex size-11 shrink-0 items-center justify-center rounded-full border border-brand/40 bg-brand/10 text-brand-deep">
                                            <Icon name="badge-check" className="size-5" />
                                        </span>
                                        <div>
                                            <h3 className="text-xl text-ink-900">{certification.name}</h3>
                                            <p className="mt-1 font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-500">
                                                {certification.issuer}
                                                {certification.year ? ` · ${certification.year}` : ''}
                                            </p>
                                        </div>
                                    </div>
                                    <p className="min-w-0 max-w-2xl text-sm leading-relaxed text-concrete-600 lg:col-span-8">
                                        {certification.description}
                                    </p>
                                </motion.div>
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            {/* ---------------------------------------------------------- */}
            {/* Leadership                                                  */}
            {/* ---------------------------------------------------------- */}
            {leadership.length > 0 && (
                <Section tone="muted" size="lg">
                    <div className="shell">
                        <SectionHeading
                            align="between"
                            eyebrow={t('team.leadership')}
                            title={t('team.hero_title')}
                            lead={t('team.hero_lead')}
                            action={
                                <Button href="/equipe" tone="outline">
                                    {t('team.title')}
                                </Button>
                            }
                        />

                        <div className="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                            {leadership.map((member, index) => (
                                <TeamCard key={member.id} member={member} index={index} />
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            {/* ---------------------------------------------------------- */}
            {/* Where to find us                                            */}
            {/* ---------------------------------------------------------- */}
            {/*
                The id is the address this section is reachable at, and the
                one the Place node in the page's structured data carries. It
                comes from the server (config `site.seo.location_anchor`) so
                the markup and the schema describing it name it once.

                The map itself is an iframe belonging to google.com, and
                nothing inside it is ever indexed as part of this page. What a
                crawler can read is what surrounds it: the heading, the postal
                address, the opening hours and a plain link to the pin — which
                is why those are real text here rather than an image or a
                tooltip.
            */}
            <Section
                size="lg"
                id={locationAnchor}
                aria-labelledby={`${locationAnchor}-title`}
            >
                <div className="shell">
                    <SectionHeading
                        eyebrow={t('about.location_eyebrow')}
                        title={t('about.location_title')}
                        lead={t('about.location_lead')}
                        titleId={`${locationAnchor}-title`}
                    />

                    <div className="mt-12 grid gap-10 lg:grid-cols-12 lg:gap-14">
                        <div className="lg:col-span-4">
                            <Reveal>
                                <address className="space-y-6 not-italic text-sm">
                                    <div className="flex items-start gap-3">
                                        <Icon name="map-pin" className="mt-0.5 size-4 shrink-0 text-brand-deep" />
                                        <span className="leading-relaxed text-concrete-700">
                                            {site.contact?.address}
                                            <br />
                                            {site.contact?.district}, {site.contact?.city}
                                            <br />
                                            {site.contact?.postal_box}
                                        </span>
                                    </div>

                                    <div className="flex items-start gap-3">
                                        <Icon name="clock" className="mt-0.5 size-4 shrink-0 text-brand-deep" />
                                        <span className="leading-relaxed text-concrete-700">
                                            {site.contact?.hours}
                                        </span>
                                    </div>

                                    <a
                                        href={`tel:${site.contact?.phone?.replace(/\s/g, '')}`}
                                        className="flex items-start gap-3 text-concrete-700 transition-colors hover:text-brand-deep"
                                    >
                                        <Icon name="phone" className="mt-0.5 size-4 shrink-0 text-brand-deep" />
                                        {site.contact?.phone}
                                    </a>
                                </address>

                                <Button href="/contact" tone="outline" className="mt-9">
                                    {t('nav.contact')}
                                </Button>
                            </Reveal>
                        </div>

                        <div className="lg:col-span-8">
                            <Reveal delay={0.12}>
                                <OfficeMap ratio="aspect-[16/10] sm:aspect-[16/9]" />
                            </Reveal>

                            {/* The overlay button on the frame is the same
                                link, but it sits above an iframe and reads as
                                chrome. This one is a plain sentence naming the
                                district and the city, which is the form a
                                crawler and a screen reader both get something
                                out of. */}
                            {site.contact?.maps_url && (
                                <Reveal delay={0.2}>
                                    <a
                                        href={site.contact.maps_url}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="group/pin mt-5 inline-flex items-center gap-2.5 text-sm text-concrete-600 transition-colors duration-400 hover:text-brand-deep"
                                    >
                                        <Icon
                                            name="map-pin"
                                            className="size-4 shrink-0 text-brand-deep"
                                            strokeWidth={2}
                                        />
                                        <span>
                                            {t('contact.find_us')} — {site.contact?.district},{' '}
                                            {site.contact?.city}, {site.contact?.country}
                                        </span>
                                        <Icon
                                            name="arrow-up-right"
                                            className="size-3.5 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/pin:translate-x-0.5 group-hover/pin:-translate-y-0.5"
                                            strokeWidth={2}
                                        />
                                    </a>
                                </Reveal>
                            )}
                        </div>
                    </div>
                </div>
            </Section>

            <PartnerStrip partners={partners} title={t('home.trusted_by')} />

            <CtaBand />
        </>
    );
}

/* ------------------------------------------------------------------ */
/* Timeline: the year scrolls with a progress line drawn alongside.    */
/* ------------------------------------------------------------------ */
function Timeline({ milestones }) {
    const { t } = useSite();
    const ref = useRef(null);
    const reduced = useReducedMotion();

    const { scrollYProgress } = useScroll({ target: ref, offset: ['start 65%', 'end 65%'] });
    const scaleY = useTransform(scrollYProgress, [0, 1], [0, 1]);

    return (
        <Section size="lg">
            <div className="shell">
                <SectionHeading
                    eyebrow={t('about.timeline_eyebrow')}
                    title={t('about.timeline_title')}
                />

                <div ref={ref} className="relative mt-14">
                    {/* Rail */}
                    <div className="absolute left-3 top-0 hidden h-full w-px bg-concrete-200 sm:block lg:left-1/2">
                        <motion.div
                            style={reduced ? { scaleY: 1 } : { scaleY }}
                            className="h-full w-full origin-top bg-brand"
                        />
                    </div>

                    <ol className="space-y-10 lg:space-y-0">
                        {milestones.map((milestone, index) => (
                            <motion.li
                                key={milestone.id}
                                initial={{ opacity: 0, y: 34 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, amount: 0.4 }}
                                transition={{ duration: 0.85 }}
                                className="relative pl-12 sm:pl-14 lg:grid lg:grid-cols-2 lg:gap-16 lg:pb-14 lg:pl-0"
                            >
                                <span className="absolute left-0 top-1.5 flex size-6 items-center justify-center rounded-full border-2 border-brand bg-concrete-50 sm:left-0.5 lg:left-1/2 lg:-translate-x-1/2">
                                    <span className="size-2 rounded-full bg-brand" />
                                </span>

                                <div
                                    className={
                                        index % 2 === 0
                                            ? 'lg:col-start-1 lg:pr-16 lg:text-right'
                                            : 'lg:col-start-2 lg:pl-16'
                                    }
                                >
                                    <span className="font-display text-[clamp(2.25rem,5vw,3.75rem)] font-black leading-none tracking-tighter text-brand">
                                        {milestone.year}
                                    </span>
                                    <h3 className="mt-3 text-xl text-ink-900 sm:text-2xl">{milestone.title}</h3>
                                    <p className="mt-4 max-w-md text-sm leading-relaxed text-concrete-600 lg:inline-block">
                                        {milestone.description}
                                    </p>
                                </div>
                            </motion.li>
                        ))}
                    </ol>
                </div>
            </div>
        </Section>
    );
}
