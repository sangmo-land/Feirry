import { Link } from '@/Components/Link';
import { useSite } from '@/Hooks';
import { Button, Eyebrow, Icon, Section, SectionHeading } from '@/Components/Ui';
import { PostCard, ServiceCard } from '@/Components/Cards';
import { TestimonialSlider } from '@/Components/Testimonials';
import { ClipReveal, Reveal, Rule, SplitText } from '@/Components/Motion';
import { Hero } from '@/Sections/Hero';
import { HorizontalProjects } from '@/Sections/HorizontalProjects';
import {
    CtaBand,
    PartnerStrip,
    ProcessTimeline,
    SplitFeature,
    StatsBand,
    ValuesGrid,
} from '@/Sections/Shared';

export default function Home({
    services = [],
    featuredProjects = [],
    processSteps = [],
    testimonials = [],
    posts = [],
    partners = [],
    certifications = [],
}) {
    const { t, site } = useSite();

    return (
        <>
            <Hero partners={partners} />

            {/* ---------------------------------------------------------- */}
            {/* Introduction + stats                                        */}
            {/* ---------------------------------------------------------- */}
            <Section id="intro" size="lg">
                <div className="shell">
                    <SplitFeature
                        eyebrow={t('home.intro_eyebrow')}
                        title={t('home.intro_title')}
                        image="/storage/sections/home-intro.jpg"
                        imageAlt="Ingénieurs FEIRRY sur un chantier à Douala"
                        stat={{ value: 16, suffix: '', label: t('home.stats_eyebrow') }}
                        action={
                            <Button href="/entreprise" tone="outline">
                                {t('about.title')}
                            </Button>
                        }
                    >
                        <p>{t('home.intro_lead')}</p>
                        <p>
                            Nos équipes couvrent la chaîne complète — études de structure, gros oeuvre,
                            second oeuvre et VRD — depuis notre siège de Bonanjo et notre base matériel
                            de Bonabéri. Cette intégration explique pourquoi nos délais tiennent :
                            nous ne dépendons pas d'une sous-traitance en cascade pour les postes critiques.
                        </p>
                        <ul className="grid gap-3 pt-2 sm:grid-cols-2">
                            {[
                                'Bureau d\'études structure intégré',
                                'Parc matériel de 42 engins en propre',
                                'Certifié ISO 9001 et ISO 45001',
                                'Garantie décennale sur chaque ouvrage',
                            ].map((item) => (
                                <li key={item} className="flex items-start gap-2.5 text-sm text-concrete-700">
                                    <Icon name="check" className="mt-0.5 size-4 shrink-0 text-brand-deep" strokeWidth={2.4} />
                                    {item}
                                </li>
                            ))}
                        </ul>
                    </SplitFeature>
                </div>

                <div className="mt-16 sm:mt-20">
                    <StatsBand />
                </div>
            </Section>

            <PartnerStrip partners={partners} />

            {/* ---------------------------------------------------------- */}
            {/* Services                                                    */}
            {/* ---------------------------------------------------------- */}
            <Section tone="muted" size="lg">
                <div className="shell">
                    <SectionHeading
                        align="between"
                        eyebrow={t('home.services_eyebrow')}
                        title={t('home.services_title')}
                        lead={t('home.services_lead')}
                        action={
                            <Button href="/metiers" tone="outline">
                                {t('common.view_all_services')}
                            </Button>
                        }
                    />

                    <div className="mt-12 grid gap-px bg-concrete-200 sm:grid-cols-2 lg:grid-cols-3">
                        {services.map((service, index) => (
                            <ServiceCard key={service.id} service={service} index={index} />
                        ))}
                    </div>
                </div>
            </Section>

            {/* ---------------------------------------------------------- */}
            {/* Projects — the pinned horizontal rail                       */}
            {/* ---------------------------------------------------------- */}
            <HorizontalProjects projects={featuredProjects} />

            {/* ---------------------------------------------------------- */}
            {/* Why us                                                      */}
            {/* ---------------------------------------------------------- */}
            <Section size="lg" className="bg-concrete-50">
                <div className="shell">
                    <SectionHeading
                        eyebrow={t('home.why_eyebrow')}
                        title={t('home.why_title')}
                        lead="Quatre engagements que nous inscrivons au contrat, pas seulement sur une plaquette."
                    />
                    <div className="mt-12">
                        <ValuesGrid />
                    </div>

                    {certifications.length > 0 && (
                        <div className="mt-14 border-t border-concrete-200 pt-10">
                            <Reveal>
                                <Eyebrow>{t('about.certifications_eyebrow')}</Eyebrow>
                            </Reveal>

                            <div className="mt-10 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                                {certifications.map((certification, index) => (
                                    <Reveal key={certification.id} delay={index * 0.07}>
                                        <div className="flex h-full flex-col">
                                            <span className="flex size-11 items-center justify-center rounded-full border border-brand/40 bg-brand/10 text-brand-deep">
                                                <Icon name="badge-check" className="size-5" />
                                            </span>
                                            <h3 className="mt-5 font-display text-base font-bold tracking-[-0.02em] text-ink-900">
                                                {certification.name}
                                            </h3>
                                            <p className="mt-1 font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-500">
                                                {certification.issuer}
                                                {certification.year ? ` · ${certification.year}` : ''}
                                            </p>
                                        </div>
                                    </Reveal>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </Section>

            {/* ---------------------------------------------------------- */}
            {/* Process                                                     */}
            {/* ---------------------------------------------------------- */}
            <Section tone="muted" size="lg">
                <div className="shell">
                    <SectionHeading
                        align="between"
                        eyebrow={t('home.process_eyebrow')}
                        title={t('home.process_title')}
                        lead={t('home.process_lead')}
                        action={
                            <Button href="/methode" tone="outline">
                                {t('process.title')}
                            </Button>
                        }
                    />

                    <div className="mt-14">
                        <ProcessTimeline steps={processSteps} compact />
                    </div>
                </div>
            </Section>

            {/* ---------------------------------------------------------- */}
            {/* Testimonials                                                */}
            {/* ---------------------------------------------------------- */}
            {testimonials.length > 0 && (
                <section className="noise-overlay relative overflow-hidden bg-ink-950 py-20 text-concrete-50 sm:py-28">
                    <div className="blueprint-grid absolute inset-0 opacity-50" />

                    <div className="shell relative">
                        <SectionHeading
                            tone="light"
                            eyebrow={t('home.testimonials_eyebrow')}
                            title={t('home.testimonials_title')}
                            lead="Six maîtres d'ouvrage, six programmes différents, une même exigence de traçabilité."
                        />

                        <div className="mt-12">
                            <TestimonialSlider testimonials={testimonials} tone="light" />
                        </div>
                    </div>
                </section>
            )}

            {/* ---------------------------------------------------------- */}
            {/* Blog                                                        */}
            {/* ---------------------------------------------------------- */}
            {posts.length > 0 && (
                <Section size="lg">
                    <div className="shell">
                        <SectionHeading
                            align="between"
                            eyebrow={t('home.blog_eyebrow')}
                            title={t('home.blog_title')}
                            action={
                                <Button href="/actualites" tone="outline">
                                    {t('common.view_all')}
                                </Button>
                            }
                        />

                        <Rule className="mt-12" />

                        <div className="mt-10 grid gap-12 lg:grid-cols-3">
                            {posts.map((post, index) => (
                                <PostCard key={post.id} post={post} index={index} />
                            ))}
                        </div>
                    </div>
                </Section>
            )}

            <CtaBand />
        </>
    );
}
