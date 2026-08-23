import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { useLocalePath, useSite } from '@/Hooks';
import { OfficeMap } from '@/Components/Map';
import { Icon, Section, SectionHeading } from '@/Components/Ui';
import { Accordion } from '@/Components/Accordion';
import { FormSuccess, Honeypot, SubmitButton, TextArea, TextField } from '@/Components/Form';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { Crumb } from '@/Sections/Shared';

export default function Contact({ faqs = [] }) {
    const { t, site } = useSite();
    const localize = useLocalePath();
    const [submitted, setSubmitted] = useState(false);

    const form = useForm({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: '',
        website: '',
    });

    const submit = (event) => {
        event.preventDefault();

        form.post(localize('/contact'), {
            preserveScroll: true,
            onSuccess: () => {
                setSubmitted(true);
                form.reset();
            },
        });
    };

    const channels = [
        {
            icon: 'phone',
            label: t('common.call_us'),
            value: site.contact?.phone,
            secondary: site.contact?.phone_secondary,
            href: `tel:${site.contact?.phone?.replace(/\s/g, '')}`,
        },
        {
            icon: 'whatsapp',
            label: t('common.whatsapp'),
            value: site.contact?.phone,
            secondary: 'Réponse la plus rapide',
            href: `https://wa.me/${site.contact?.whatsapp}`,
        },
        {
            icon: 'mail',
            label: t('common.write_us'),
            value: site.contact?.email,
            secondary: site.contact?.email_devis,
            href: `mailto:${site.contact?.email}`,
        },
    ];

    return (
        <>
            <PageHero
                title={t('contact.hero_title')}
                lead={t('contact.hero_lead')}
                image="/storage/heroes/contact.jpg"
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{t('nav.contact')}</Crumb>
                    </>
                }
            />

            {/* Channels */}
            <section className="border-b border-concrete-200 bg-concrete-100">
                <div className="shell">
                    <div className="grid gap-px bg-concrete-200 sm:grid-cols-3">
                        {channels.map((channel, index) => (
                            <motion.a
                                key={channel.label}
                                href={channel.href}
                                target={channel.href.startsWith('http') ? '_blank' : undefined}
                                rel="noreferrer"
                                initial={{ opacity: 0, y: 20 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, amount: 0.4 }}
                                transition={{ duration: 0.7, delay: index * 0.08 }}
                                className="group/ch relative overflow-hidden bg-concrete-100 p-8 sm:p-10"
                            >
                                <span className="pointer-events-none absolute inset-0 translate-y-full bg-ink-900 transition-transform duration-[0.9s] [transition-timing-function:var(--ease-out-expo)] group-hover/ch:translate-y-0" />

                                <span className="relative flex size-12 items-center justify-center border border-concrete-300 text-ink-900 transition-colors duration-600 group-hover/ch:border-brand group-hover/ch:bg-brand">
                                    <Icon name={channel.icon} className="size-5" />
                                </span>

                                <span className="relative mt-6 block font-mono text-[0.625rem] uppercase tracking-[0.16em] text-concrete-500 transition-colors duration-600 group-hover/ch:text-concrete-400">
                                    {channel.label}
                                </span>
                                <span className="relative mt-2 block font-display text-lg font-bold tracking-[-0.02em] text-ink-900 transition-colors duration-600 group-hover/ch:text-concrete-50">
                                    {channel.value}
                                </span>
                                {channel.secondary && (
                                    <span className="relative mt-1 block text-xs text-concrete-500 transition-colors duration-600 group-hover/ch:text-concrete-400">
                                        {channel.secondary}
                                    </span>
                                )}
                            </motion.a>
                        ))}
                    </div>
                </div>
            </section>

            <Section size="lg">
                <div className="shell grid gap-16 lg:grid-cols-12 lg:gap-20">
                    {/* Form */}
                    <div className="min-w-0 lg:col-span-7">
                        {submitted ? (
                            <FormSuccess
                                title={t('contact.form_title')}
                                message={t('contact.success')}
                                onReset={() => setSubmitted(false)}
                                resetLabel={t('common.send')}
                            />
                        ) : (
                            <form onSubmit={submit}>
                                <SectionHeading
                                    eyebrow={t('contact.form_title')}
                                    title="Dites-nous en quelques lignes"
                                />

                                <p className="mt-6 text-xs text-concrete-500">{t('common.required_fields')}</p>

                                <div className="mt-10 space-y-8">
                                    <div className="grid gap-8 sm:grid-cols-2">
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
                                    </div>

                                    <div className="grid gap-8 sm:grid-cols-2">
                                        <TextField
                                            label={`${t('contact.phone')} (${t('common.optional')})`}
                                            type="tel"
                                            placeholder="+237 6 XX XX XX XX"
                                            value={form.data.phone}
                                            onChange={(event) => form.setData('phone', event.target.value)}
                                            error={form.errors.phone}
                                            autoComplete="tel"
                                        />
                                        <TextField
                                            label={`${t('contact.subject')} (${t('common.optional')})`}
                                            value={form.data.subject}
                                            onChange={(event) => form.setData('subject', event.target.value)}
                                            error={form.errors.subject}
                                        />
                                    </div>

                                    <TextArea
                                        label={t('contact.message')}
                                        required
                                        rows={7}
                                        value={form.data.message}
                                        onChange={(event) => form.setData('message', event.target.value)}
                                        error={form.errors.message}
                                    />

                                    <Honeypot
                                        value={form.data.website}
                                        onChange={(value) => form.setData('website', value)}
                                    />
                                </div>

                                <div className="mt-10 flex flex-wrap items-center gap-8">
                                    <SubmitButton processing={form.processing}>{t('common.send')}</SubmitButton>

                                    <p className="max-w-xs text-xs leading-relaxed text-concrete-500">
                                        En envoyant ce formulaire vous acceptez notre{' '}
                                        <Link href="/confidentialite" className="underline underline-offset-2">
                                            politique de confidentialité
                                        </Link>
                                        .
                                    </p>
                                </div>
                            </form>
                        )}
                    </div>

                    {/* Office details */}
                    <aside className="min-w-0 lg:col-span-5">
                        <div className="lg:sticky lg:top-32">
                            <Reveal>
                                <div className="border border-concrete-200 bg-concrete-100 p-8 sm:p-10">
                                    <span className="eyebrow text-concrete-500">{t('contact.office')}</span>

                                    <address className="mt-7 space-y-6 not-italic">
                                        <div className="flex items-start gap-4">
                                            <Icon name="map-pin" className="mt-0.5 size-4 shrink-0 text-brand-deep" />
                                            <div className="text-sm leading-relaxed text-concrete-700">
                                                {site.contact?.address}
                                                <br />
                                                {site.contact?.district}, {site.contact?.city}
                                                <br />
                                                {site.contact?.postal_box}
                                                <br />
                                                {site.contact?.country}
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-4">
                                            <Icon name="clock" className="mt-0.5 size-4 shrink-0 text-brand-deep" />
                                            <div className="text-sm leading-relaxed text-concrete-700">
                                                <span className="block font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-500">
                                                    {t('contact.hours')}
                                                </span>
                                                <span className="mt-1 block">{site.contact?.hours}</span>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-4">
                                            <Icon name="file-text" className="mt-0.5 size-4 shrink-0 text-brand-deep" />
                                            <div className="text-sm leading-relaxed text-concrete-700">
                                                <span className="block font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-500">
                                                    {t('footer.rccm')} / {t('footer.niu')}
                                                </span>
                                                <span className="mt-1 block">{site.legal?.rccm}</span>
                                                <span className="block">{site.legal?.niu}</span>
                                            </div>
                                        </div>
                                    </address>

                                    <a
                                        href={site.contact?.maps_url}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="group/map mt-8 flex items-center justify-between border-t border-concrete-200 pt-7 font-mono text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-ink-900"
                                    >
                                        {t('contact.find_us')}
                                        <Icon
                                            name="external-link"
                                            className="size-4 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/map:translate-x-1 group-hover/map:-translate-y-0.5"
                                            strokeWidth={2}
                                        />
                                    </a>
                                </div>
                            </Reveal>

                            {/* OpenStreetMap here rather than Google: its embed
                                needs no API key and sets no advertising cookies,
                                and this page already asks the visitor for their
                                details. The company page uses Google. */}
                            <Reveal delay={0.12} className="mt-8">
                                <OfficeMap provider="osm" />
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
                                <SectionHeading eyebrow={t('faq.title')} title={t('faq.hero_title')} />
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
