import { useMemo, useState } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { Link } from '@/Components/Link';
import { AnimatePresence, motion } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { useLocalePath, useSite } from '@/Hooks';
import { Icon, Section, SectionHeading } from '@/Components/Ui';
import { Accordion } from '@/Components/Accordion';
import {
    FileField,
    FormSuccess,
    Honeypot,
    OptionCards,
    SelectField,
    SubmitButton,
    TextArea,
    TextField,
} from '@/Components/Form';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { Crumb } from '@/Sections/Shared';

const PROJECT_TYPES = [
    { value: 'residential', icon: 'home' },
    { value: 'collective', icon: 'building' },
    { value: 'commercial', icon: 'briefcase' },
    { value: 'industrial', icon: 'factory' },
    { value: 'public', icon: 'landmark' },
    { value: 'renovation', icon: 'wrench' },
    { value: 'infrastructure', icon: 'route' },
    { value: 'other' },
];

const BUDGETS = ['under_10', '10_50', '50_150', '150_500', 'over_500', 'undecided'];
const TIMELINES = ['urgent', '3_months', '6_months', 'year', 'exploring'];

const STEPS = ['step_project', 'step_details', 'step_contact'];

export default function Quote({ services = [], faqs = [] }) {
    const { t } = useSite();
    const localize = useLocalePath();
    const { props } = usePage();
    const [step, setStep] = useState(0);
    const [submitted, setSubmitted] = useState(false);

    const form = useForm({
        service_id: '',
        project_type: '',
        budget_range: '',
        timeline: '',
        location: '',
        surface_m2: '',
        name: '',
        company: '',
        email: '',
        phone: '',
        message: '',
        attachment: null,
        website: '',
    });

    const stepValid = useMemo(() => {
        if (step === 0) return Boolean(form.data.project_type);
        if (step === 1) return Boolean(form.data.location);
        return Boolean(form.data.name && form.data.email && form.data.phone);
    }, [step, form.data]);

    const submit = (event) => {
        event.preventDefault();

        form.post(localize('/devis'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setSubmitted(true);
                form.reset();
                setStep(0);
            },
            onError: (errors) => {
                // Jump back to the first step that holds a rejected field.
                if (errors.project_type) setStep(0);
                else if (errors.location || errors.surface_m2) setStep(1);
                else setStep(2);
            },
        });
    };

    return (
        <>
            <PageHero
                title={t('quote.hero_title')}
                lead={t('quote.hero_lead')}
                image="/storage/heroes/quote.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.quote')}</Crumb>
                    </>
                }
            >
                <ul className="flex flex-wrap gap-x-8 gap-y-3">
                    {['guarantee_1', 'guarantee_2', 'guarantee_3'].map((key) => (
                        <li
                            key={key}
                            className="inline-flex items-center gap-2.5 font-mono text-[0.6875rem] uppercase tracking-[0.13em] text-concrete-300"
                        >
                            <Icon name="check" className="size-4 text-brand" strokeWidth={2.5} />
                            {t(`quote.${key}`)}
                        </li>
                    ))}
                </ul>
            </PageHero>

            <Section size="lg">
                <div className="shell grid gap-16 lg:grid-cols-12 lg:gap-20">
                    <div className="min-w-0 lg:col-span-8">
                        {submitted ? (
                            <FormSuccess
                                title={t('quote.title')}
                                message={t('quote.success', { reference: props.flash?.reference ?? '' })}
                                reference={props.flash?.reference}
                                onReset={() => setSubmitted(false)}
                                resetLabel={t('quote.submit')}
                            />
                        ) : (
                            <form onSubmit={submit}>
                                {/* Step rail */}
                                <ol className="flex flex-wrap gap-x-8 gap-y-3 border-b border-concrete-200 pb-6">
                                    {STEPS.map((key, index) => (
                                        <li key={key}>
                                            <button
                                                type="button"
                                                onClick={() => index < step && setStep(index)}
                                                disabled={index > step}
                                                className={cn(
                                                    'flex items-center gap-3 font-mono text-[0.6875rem] font-semibold uppercase tracking-[0.14em] transition-colors duration-400',
                                                    index === step
                                                        ? 'text-ink-900'
                                                        : index < step
                                                          ? 'text-concrete-500 hover:text-ink-900'
                                                          : 'cursor-not-allowed text-concrete-300',
                                                )}
                                            >
                                                <span
                                                    className={cn(
                                                        'flex size-7 items-center justify-center rounded-full border transition-colors duration-400',
                                                        index === step
                                                            ? 'border-brand bg-brand text-ink-950'
                                                            : index < step
                                                              ? 'border-jade bg-jade text-white'
                                                              : 'border-concrete-300',
                                                    )}
                                                >
                                                    {index < step ? (
                                                        <Icon name="check" className="size-3.5" strokeWidth={3} />
                                                    ) : (
                                                        index + 1
                                                    )}
                                                </span>
                                                {t(`quote.${key}`)}
                                            </button>
                                        </li>
                                    ))}
                                </ol>

                                {/* Progress */}
                                <div className="mt-4 h-0.5 w-full bg-concrete-200">
                                    <motion.div
                                        className="h-full origin-left bg-brand"
                                        animate={{ scaleX: (step + 1) / STEPS.length }}
                                        transition={{ duration: 0.6, ease: EASE.outExpo }}
                                        style={{ transformOrigin: 'left' }}
                                    />
                                </div>

                                <div className="mt-12 min-h-[26rem]">
                                    <AnimatePresence mode="wait">
                                        <motion.div
                                            key={step}
                                            initial={{ opacity: 0, x: 24 }}
                                            animate={{ opacity: 1, x: 0 }}
                                            exit={{ opacity: 0, x: -24 }}
                                            transition={{ duration: 0.45, ease: EASE.outExpo }}
                                        >
                                            {step === 0 && (
                                                <div className="space-y-10">
                                                    <OptionCards
                                                        label={t('quote.project_type')}
                                                        name="project_type"
                                                        required
                                                        columns={2}
                                                        value={form.data.project_type}
                                                        onChange={(value) => form.setData('project_type', value)}
                                                        error={form.errors.project_type}
                                                        options={PROJECT_TYPES.map((type) => ({
                                                            value: type.value,
                                                            icon: type.icon,
                                                            label: t(`quote.types.${type.value}`),
                                                        }))}
                                                    />

                                                    <SelectField
                                                        label={`${t('quote.service')} (${t('common.optional')})`}
                                                        placeholder="—"
                                                        value={form.data.service_id}
                                                        onChange={(event) =>
                                                            form.setData('service_id', event.target.value)
                                                        }
                                                        error={form.errors.service_id}
                                                        options={services.map((service) => ({
                                                            value: service.id,
                                                            label: service.title,
                                                        }))}
                                                    />
                                                </div>
                                            )}

                                            {step === 1 && (
                                                <div className="space-y-10">
                                                    <div className="grid gap-8 sm:grid-cols-2">
                                                        <TextField
                                                            label={t('quote.location')}
                                                            required
                                                            placeholder="Bonapriso, Douala"
                                                            value={form.data.location}
                                                            onChange={(event) =>
                                                                form.setData('location', event.target.value)
                                                            }
                                                            error={form.errors.location}
                                                        />
                                                        <TextField
                                                            label={`${t('quote.surface')} (${t('common.optional')})`}
                                                            type="number"
                                                            min="1"
                                                            placeholder="450"
                                                            value={form.data.surface_m2}
                                                            onChange={(event) =>
                                                                form.setData('surface_m2', event.target.value)
                                                            }
                                                            error={form.errors.surface_m2}
                                                        />
                                                    </div>

                                                    <OptionCards
                                                        label={t('quote.budget')}
                                                        name="budget_range"
                                                        columns={3}
                                                        value={form.data.budget_range}
                                                        onChange={(value) => form.setData('budget_range', value)}
                                                        error={form.errors.budget_range}
                                                        options={BUDGETS.map((key) => ({
                                                            value: key,
                                                            label: t(`quote.budgets.${key}`),
                                                        }))}
                                                    />

                                                    <OptionCards
                                                        label={t('quote.timeline')}
                                                        name="timeline"
                                                        columns={3}
                                                        value={form.data.timeline}
                                                        onChange={(value) => form.setData('timeline', value)}
                                                        error={form.errors.timeline}
                                                        options={TIMELINES.map((key) => ({
                                                            value: key,
                                                            label: t(`quote.timelines.${key}`),
                                                        }))}
                                                    />
                                                </div>
                                            )}

                                            {step === 2 && (
                                                <div className="space-y-8">
                                                    <div className="grid gap-8 sm:grid-cols-2">
                                                        <TextField
                                                            label={t('contact.name')}
                                                            required
                                                            value={form.data.name}
                                                            onChange={(event) =>
                                                                form.setData('name', event.target.value)
                                                            }
                                                            error={form.errors.name}
                                                            autoComplete="name"
                                                        />
                                                        <TextField
                                                            label={`${t('contact.company')} (${t('common.optional')})`}
                                                            value={form.data.company}
                                                            onChange={(event) =>
                                                                form.setData('company', event.target.value)
                                                            }
                                                            error={form.errors.company}
                                                            autoComplete="organization"
                                                        />
                                                    </div>

                                                    <div className="grid gap-8 sm:grid-cols-2">
                                                        <TextField
                                                            label={t('contact.email')}
                                                            type="email"
                                                            required
                                                            value={form.data.email}
                                                            onChange={(event) =>
                                                                form.setData('email', event.target.value)
                                                            }
                                                            error={form.errors.email}
                                                            autoComplete="email"
                                                        />
                                                        <TextField
                                                            label={t('contact.phone')}
                                                            type="tel"
                                                            required
                                                            placeholder="+237 6 XX XX XX XX"
                                                            value={form.data.phone}
                                                            onChange={(event) =>
                                                                form.setData('phone', event.target.value)
                                                            }
                                                            error={form.errors.phone}
                                                            autoComplete="tel"
                                                        />
                                                    </div>

                                                    <TextArea
                                                        label={`${t('quote.message')} (${t('common.optional')})`}
                                                        rows={5}
                                                        value={form.data.message}
                                                        onChange={(event) =>
                                                            form.setData('message', event.target.value)
                                                        }
                                                        error={form.errors.message}
                                                    />

                                                    <FileField
                                                        label={`${t('quote.attachment')} (${t('common.optional')})`}
                                                        hint="PDF, JPG, PNG, DWG ou ZIP — 10 Mo maximum"
                                                        accept=".pdf,.jpg,.jpeg,.png,.dwg,.zip"
                                                        value={form.data.attachment}
                                                        onChange={(file) => form.setData('attachment', file)}
                                                        error={form.errors.attachment}
                                                    />

                                                    <Honeypot
                                                        value={form.data.website}
                                                        onChange={(value) => form.setData('website', value)}
                                                    />
                                                </div>
                                            )}
                                        </motion.div>
                                    </AnimatePresence>
                                </div>

                                {form.progress && (
                                    <div className="mt-6 h-0.5 w-full bg-concrete-200">
                                        <div
                                            className="h-full bg-brand transition-[width] duration-200"
                                            style={{ width: `${form.progress.percentage}%` }}
                                        />
                                    </div>
                                )}

                                {/* Navigation */}
                                <div className="mt-12 flex flex-wrap items-center gap-5 border-t border-concrete-200 pt-8">
                                    {step > 0 && (
                                        <button
                                            type="button"
                                            onClick={() => setStep(step - 1)}
                                            className="group/prev inline-flex items-center gap-2.5 font-mono text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-concrete-500 transition-colors hover:text-ink-900"
                                        >
                                            <Icon
                                                name="arrow-left"
                                                className="size-4 transition-transform duration-500 group-hover/prev:-translate-x-1"
                                                strokeWidth={2}
                                            />
                                            {t('common.previous')}
                                        </button>
                                    )}

                                    <div className="ml-auto flex items-center gap-5">
                                        {step < STEPS.length - 1 ? (
                                            <button
                                                type="button"
                                                onClick={() => stepValid && setStep(step + 1)}
                                                disabled={!stepValid}
                                                className={cn(
                                                    'group/next inline-flex items-center gap-3 rounded-full px-9 py-4',
                                                    'text-[0.8125rem] font-semibold uppercase tracking-[0.14em] transition-colors duration-500',
                                                    stepValid
                                                        ? 'bg-ink-900 text-concrete-50 hover:bg-ink-800'
                                                        : 'cursor-not-allowed bg-concrete-200 text-concrete-400',
                                                )}
                                            >
                                                {t('common.next')}
                                                <Icon
                                                    name="arrow-right"
                                                    className="size-4 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/next:translate-x-1.5"
                                                    strokeWidth={2}
                                                />
                                            </button>
                                        ) : (
                                            <SubmitButton processing={form.processing}>
                                                {t('quote.submit')}
                                            </SubmitButton>
                                        )}
                                    </div>
                                </div>

                                <p className="mt-6 text-xs leading-relaxed text-concrete-500">
                                    {t('common.required_fields')} Vos données servent uniquement à traiter cette
                                    demande —{' '}
                                    <Link href="/confidentialite" className="underline underline-offset-2">
                                        politique de confidentialité
                                    </Link>
                                    .
                                </p>
                            </form>
                        )}
                    </div>

                    {/* Reassurance rail */}
                    <aside className="min-w-0 lg:col-span-4">
                        <div className="lg:sticky lg:top-32">
                            <Reveal>
                                <div className="noise-overlay relative overflow-hidden bg-ink-900 p-8 text-concrete-100 sm:p-10">
                                    <div className="blueprint-grid absolute inset-0 opacity-60" />

                                    <div className="relative">
                                        <span className="eyebrow text-brand">Ce qui se passe ensuite</span>

                                        <ol className="mt-8 space-y-7">
                                            {[
                                                {
                                                    title: 'Nous vous rappelons',
                                                    text: 'Un ingénieur vous contacte sous 48 heures ouvrées pour cadrer le besoin.',
                                                },
                                                {
                                                    title: 'Visite technique',
                                                    text: 'Gratuite dans l’agglomération de Douala. Nous mesurons ce que le site autorise.',
                                                },
                                                {
                                                    title: 'Devis descriptif',
                                                    text: 'Chaque poste décrit, quantifié et daté. Comparable ligne à ligne.',
                                                },
                                            ].map((item, index) => (
                                                <li key={item.title} className="flex gap-4">
                                                    <span className="flex size-8 shrink-0 items-center justify-center rounded-full border border-ink-600 font-mono text-[0.625rem] text-brand">
                                                        {index + 1}
                                                    </span>
                                                    <div>
                                                        <span className="block font-display text-base font-bold tracking-[-0.02em] text-concrete-50">
                                                            {item.title}
                                                        </span>
                                                        <span className="mt-1.5 block text-xs leading-relaxed text-concrete-400">
                                                            {item.text}
                                                        </span>
                                                    </div>
                                                </li>
                                            ))}
                                        </ol>
                                    </div>
                                </div>
                            </Reveal>

                            <Reveal delay={0.12} className="mt-8">
                                <div className="border border-concrete-200 bg-concrete-100 p-8">
                                    <Icon name="phone" className="size-5 text-brand-deep" />
                                    <p className="mt-4 font-display text-base font-bold leading-snug tracking-[-0.02em] text-ink-900">
                                        Vous préférez en parler ?
                                    </p>
                                    <QuoteContact />
                                </div>
                            </Reveal>
                        </div>
                    </aside>
                </div>
            </Section>

            {faqs.length > 0 && (
                <Section tone="muted" size="lg">
                    <div className="shell grid gap-14 lg:grid-cols-12 lg:gap-20">
                        <div className="min-w-0 lg:col-span-4">
                            <div className="lg:sticky lg:top-32">
                                <SectionHeading eyebrow={t('faq.title')} title="Devis et paiement" />
                            </div>
                        </div>
                        <div className="min-w-0 lg:col-span-8">
                            <Accordion items={faqs} defaultOpen={0} />
                        </div>
                    </div>
                </Section>
            )}
        </>
    );
}

function QuoteContact() {
    const { site, t } = useSite();

    return (
        <div className="mt-5 space-y-3 text-sm">
            <a
                href={`tel:${site.contact?.phone?.replace(/\s/g, '')}`}
                className="flex items-center gap-3 text-ink-900 transition-colors hover:text-brand-deep"
            >
                <Icon name="phone" className="size-4 shrink-0 text-concrete-400" />
                {site.contact?.phone}
            </a>
            <a
                href={`https://wa.me/${site.contact?.whatsapp}`}
                target="_blank"
                rel="noreferrer"
                className="flex items-center gap-3 text-ink-900 transition-colors hover:text-jade"
            >
                <Icon name="whatsapp" className="size-4 shrink-0 text-concrete-400" />
                {t('common.whatsapp')}
            </a>
            <a
                href={`mailto:${site.contact?.email_devis}`}
                className="flex items-center gap-3 break-all text-ink-900 transition-colors hover:text-brand-deep"
            >
                <Icon name="mail" className="size-4 shrink-0 text-concrete-400" />
                {site.contact?.email_devis}
            </a>
        </div>
    );
}
