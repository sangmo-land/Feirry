import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { formatDate } from '@/Lib/utils';
import { useLocalePath, useSite } from '@/Hooks';
import { Badge, Icon, Section, SectionHeading } from '@/Components/Ui';
import { JobCard } from '@/Components/Cards';
import { FileField, FormSuccess, Honeypot, SubmitButton, TextArea, TextField } from '@/Components/Form';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { Crumb } from '@/Sections/Shared';

export default function CareerShow({ opening, others = [] }) {
    const { t, locale, site } = useSite();
    const localize = useLocalePath();
    const [submitted, setSubmitted] = useState(false);

    const form = useForm({
        name: '',
        email: '',
        phone: '',
        linkedin: '',
        years_experience: '',
        cover_letter: '',
        cv: null,
        website: '',
    });

    const submit = (event) => {
        event.preventDefault();

        form.post(localize(`/carrieres/${opening.slug}/candidature`), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setSubmitted(true);
                form.reset();
            },
        });
    };

    return (
        <>
            <PageHero
                title={opening.title}
                image="/storage/heroes/career-detail.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb href="/carrieres">{t('nav.careers')}</Crumb>
                        <Crumb last>{opening.title}</Crumb>
                    </>
                }
            >
                <div className="flex flex-wrap gap-3">
                    <Badge tone="brand">{opening.contract_type}</Badge>
                    <Badge tone="outline">{opening.location}</Badge>
                    {opening.experience_level && <Badge tone="outline">{opening.experience_level}</Badge>}
                </div>
            </PageHero>

            {/* Key facts */}
            <section className="border-b border-concrete-200 bg-concrete-100">
                <div className="shell">
                    <dl className="grid gap-px bg-concrete-200 sm:grid-cols-2 lg:grid-cols-4">
                        {[
                            { label: t('careers.department'), value: opening.department, icon: 'layers' },
                            { label: t('careers.contract'), value: opening.contract_type, icon: 'file-text' },
                            { label: t('careers.experience'), value: opening.experience_level, icon: 'trending-up' },
                            { label: t('careers.salary'), value: opening.salary_range, icon: 'banknote' },
                        ]
                            .filter((fact) => fact.value)
                            .map((fact, index) => (
                                <motion.div
                                    key={fact.label}
                                    initial={{ opacity: 0, y: 16 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, amount: 0.5 }}
                                    transition={{ duration: 0.6, delay: index * 0.05 }}
                                    className="bg-concrete-100 p-6 sm:p-8"
                                >
                                    <dt className="flex items-center gap-2 font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-500">
                                        <Icon name={fact.icon} className="size-3.5 text-brand-deep" />
                                        {fact.label}
                                    </dt>
                                    <dd className="mt-2.5 font-display text-base font-bold tracking-[-0.02em] text-ink-900">
                                        {fact.value}
                                    </dd>
                                </motion.div>
                            ))}
                    </dl>
                </div>
            </section>

            <Section size="lg">
                <div className="shell grid gap-16 lg:grid-cols-12 lg:gap-20">
                    {/* Description */}
                    <div className="min-w-0 lg:col-span-7">
                        {opening.description && (
                            <Reveal>
                                <div
                                    className="prose prose-brand max-w-none prose-p:leading-relaxed"
                                    dangerouslySetInnerHTML={{ __html: opening.description }}
                                />
                            </Reveal>
                        )}

                        {opening.requirements?.length > 0 && (
                            <Reveal delay={0.1} className="mt-10">
                                <h2 className="text-2xl text-ink-900">{t('careers.requirements')}</h2>
                                <ul className="mt-7 space-y-3.5">
                                    {opening.requirements.map((item, index) => (
                                        <motion.li
                                            key={item}
                                            initial={{ opacity: 0, x: -12 }}
                                            whileInView={{ opacity: 1, x: 0 }}
                                            viewport={{ once: true, amount: 0.5 }}
                                            transition={{ duration: 0.55, delay: index * 0.05 }}
                                            className="flex items-start gap-3 text-sm text-concrete-700 sm:text-base"
                                        >
                                            <Icon
                                                name="check"
                                                className="mt-1 size-4 shrink-0 text-brand-deep"
                                                strokeWidth={2.4}
                                            />
                                            {item}
                                        </motion.li>
                                    ))}
                                </ul>
                            </Reveal>
                        )}

                        {opening.benefits?.length > 0 && (
                            <Reveal delay={0.1} className="mt-10">
                                <h2 className="text-2xl text-ink-900">{t('careers.benefits')}</h2>
                                <ul className="mt-7 grid gap-3 sm:grid-cols-2">
                                    {opening.benefits.map((item) => (
                                        <li
                                            key={item}
                                            className="flex items-start gap-3 border border-concrete-200 bg-concrete-100 p-4 text-sm text-concrete-700"
                                        >
                                            <Icon name="sparkles" className="mt-0.5 size-4 shrink-0 text-jade" />
                                            {item}
                                        </li>
                                    ))}
                                </ul>
                            </Reveal>
                        )}

                        {opening.closes_at && (
                            <Reveal delay={0.15} className="mt-12">
                                <p className="inline-flex items-center gap-2.5 border border-clay/30 bg-clay/5 px-5 py-3 font-mono text-[0.625rem] uppercase tracking-[0.14em] text-clay">
                                    <Icon name="calendar" className="size-3.5" />
                                    {t('careers.closes_on')} {formatDate(opening.closes_at, locale)}
                                </p>
                            </Reveal>
                        )}
                    </div>

                    {/* Application form */}
                    <aside className="min-w-0 lg:col-span-5">
                        <div id="postuler" className="scroll-mt-32 lg:sticky lg:top-32">
                            {submitted ? (
                                <FormSuccess
                                    title={t('careers.apply_title')}
                                    message={t('careers.success')}
                                    onReset={() => setSubmitted(false)}
                                    resetLabel={t('careers.apply')}
                                />
                            ) : (
                                <form
                                    onSubmit={submit}
                                    className="border border-concrete-200 bg-concrete-100 p-8 sm:p-10"
                                >
                                    <h2 className="text-2xl text-ink-900">{t('careers.apply_title')}</h2>
                                    <p className="mt-3 text-xs text-concrete-500">{t('common.required_fields')}</p>

                                    <div className="mt-8 space-y-6">
                                        <TextField
                                            label={t('contact.name')}
                                            required
                                            value={form.data.name}
                                            onChange={(event) => form.setData('name', event.target.value)}
                                            error={form.errors.name}
                                            autoComplete="name"
                                        />

                                        <TextField
                                            label={t('contact.email')}
                                            type="email"
                                            required
                                            value={form.data.email}
                                            onChange={(event) => form.setData('email', event.target.value)}
                                            error={form.errors.email}
                                            autoComplete="email"
                                        />

                                        <TextField
                                            label={t('contact.phone')}
                                            type="tel"
                                            required
                                            placeholder="+237 6 XX XX XX XX"
                                            value={form.data.phone}
                                            onChange={(event) => form.setData('phone', event.target.value)}
                                            error={form.errors.phone}
                                            autoComplete="tel"
                                        />

                                        <div className="grid gap-6 sm:grid-cols-2">
                                            <TextField
                                                label={`${t('careers.experience')} (${t('common.optional')})`}
                                                type="number"
                                                min="0"
                                                max="60"
                                                value={form.data.years_experience}
                                                onChange={(event) =>
                                                    form.setData('years_experience', event.target.value)
                                                }
                                                error={form.errors.years_experience}
                                            />

                                            <TextField
                                                label={`LinkedIn (${t('common.optional')})`}
                                                type="url"
                                                placeholder="https://"
                                                value={form.data.linkedin}
                                                onChange={(event) => form.setData('linkedin', event.target.value)}
                                                error={form.errors.linkedin}
                                            />
                                        </div>

                                        <FileField
                                            label={t('careers.cv')}
                                            required
                                            accept=".pdf,.doc,.docx"
                                            value={form.data.cv}
                                            onChange={(file) => form.setData('cv', file)}
                                            error={form.errors.cv}
                                        />

                                        <TextArea
                                            label={`${t('careers.cover_letter')} (${t('common.optional')})`}
                                            rows={5}
                                            value={form.data.cover_letter}
                                            onChange={(event) => form.setData('cover_letter', event.target.value)}
                                            error={form.errors.cover_letter}
                                        />

                                        <Honeypot
                                            value={form.data.website}
                                            onChange={(value) => form.setData('website', value)}
                                        />
                                    </div>

                                    {form.progress && (
                                        <div className="mt-6 h-0.5 w-full bg-concrete-200">
                                            <div
                                                className="h-full bg-brand transition-[width] duration-200"
                                                style={{ width: `${form.progress.percentage}%` }}
                                            />
                                        </div>
                                    )}

                                    <SubmitButton processing={form.processing} className="mt-8 w-full">
                                        {t('careers.apply')}
                                    </SubmitButton>

                                    <p className="mt-5 text-center text-xs leading-relaxed text-concrete-500">
                                        Vos données sont utilisées uniquement pour traiter cette candidature.
                                        Voir la{' '}
                                        <Link href="/confidentialite" className="underline underline-offset-2">
                                            politique de confidentialité
                                        </Link>
                                        .
                                    </p>
                                </form>
                            )}
                        </div>
                    </aside>
                </div>
            </Section>

            {others.length > 0 && (
                <Section tone="muted" size="lg">
                    <div className="shell">
                        <SectionHeading eyebrow={t('careers.open_positions')} title="Autres postes ouverts" />
                        <div className="mt-12 border-t border-concrete-200">
                            {others.map((job, index) => (
                                <JobCard key={job.id} job={job} index={index} />
                            ))}
                        </div>
                    </div>
                </Section>
            )}
        </>
    );
}
