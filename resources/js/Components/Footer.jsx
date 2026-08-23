import { useEffect, useRef } from 'react';
import { useForm } from '@inertiajs/react';
import { motion, useScroll, useTransform } from 'motion/react';
import { Link } from '@/Components/Link';
import { cn, EASE } from '@/Lib/utils';
import { useLocalePath, useMediaQuery, useReducedMotion, useSite } from '@/Hooks';
import { Icon } from '@/Components/Ui';
import { Logo as BrandLogo, LogoWordmark } from '@/Components/Logo';
import { SplitText } from '@/Components/Motion';

const FOOTER_NAV = {
    company: [
        { key: 'nav.about', href: '/entreprise' },
        { key: 'nav.process', href: '/methode' },
        { key: 'nav.team', href: '/equipe' },
        { key: 'nav.careers', href: '/carrieres' },
        { key: 'nav.blog', href: '/actualites' },
        { key: 'nav.faq', href: '/faq' },
    ],
    expertise: [
        { label: 'Gros oeuvre & structure', href: '/metiers/gros-oeuvre' },
        { label: 'Second oeuvre & finitions', href: '/metiers/second-oeuvre' },
        { label: 'Génie civil & VRD', href: '/metiers/genie-civil-vrd' },
        { label: 'Rénovation', href: '/metiers/renovation-rehabilitation' },
        { label: 'Études & conception', href: '/metiers/etudes-conception' },
        { label: 'Contractant général', href: '/metiers/contractant-general' },
    ],
};

const SOCIAL_ICONS = {
    facebook: 'facebook',
    linkedin: 'linkedin',
    instagram: 'instagram',
    tiktok: 'tiktok',
    youtube: 'youtube',
};

function Newsletter() {
    const { t } = useSite();
    const localize = useLocalePath();
    const { data, setData, post, processing, wasSuccessful, errors, reset } = useForm({
        email: '',
        website: '',
    });

    const submit = (event) => {
        event.preventDefault();
        post(localize('/newsletter'), {
            preserveScroll: true,
            onSuccess: () => reset('email'),
        });
    };

    return (
        <form onSubmit={submit} className="mt-6">
            <label htmlFor="footer-newsletter" className="sr-only">
                {t('blog.newsletter_title')}
            </label>

            <div className="flex items-center border-b border-ink-600 transition-colors focus-within:border-brand">
                <input
                    id="footer-newsletter"
                    type="email"
                    required
                    value={data.email}
                    onChange={(event) => setData('email', event.target.value)}
                    placeholder={t('blog.newsletter_placeholder')}
                    className="min-w-0 flex-1 bg-transparent py-3 text-sm text-concrete-100 outline-none placeholder:text-concrete-600"
                />
                <input
                    type="text"
                    tabIndex={-1}
                    autoComplete="off"
                    value={data.website}
                    onChange={(event) => setData('website', event.target.value)}
                    className="hidden"
                    aria-hidden="true"
                />
                <button
                    type="submit"
                    disabled={processing}
                    aria-label={t('blog.newsletter_cta')}
                    className="flex size-10 shrink-0 items-center justify-center text-brand transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] hover:translate-x-1 disabled:opacity-50"
                >
                    <Icon name={processing ? 'loader-circle' : 'arrow-right'} className={cn('size-5', processing && 'animate-spin')} strokeWidth={2} />
                </button>
            </div>

            {errors.email && <p className="mt-2 text-xs text-clay">{errors.email}</p>}
            {wasSuccessful && <p className="mt-2 text-xs text-jade-bright">{t('blog.newsletter_success')}</p>}
        </form>
    );
}

/* ------------------------------------------------------------------ */
/* The oversized logotype that closes the page.                        */
/*                                                                     */
/* The real logotype rather than the name set in Archivo: at this size */
/* the difference in the letterforms is the whole point of the panel.  */
/*                                                                     */
/* Three gestures, none of which loop. A watermark that moves under    */
/* its own steam pulls the eye off the links beside it, so each of     */
/* these is spent by something the reader does:                        */
/*                                                                     */
/*   1. it rises out of its own mask the first time the band is        */
/*      reached — the hero headline's treatment at the scale of the    */
/*      footer, so the page opens and closes on the same gesture;      */
/*   2. it drifts against the scroll while the band is on screen, so   */
/*      the name reads as sitting behind the page rather than on it.   */
/*      Around twenty pixels across the whole pass, felt more than     */
/*      seen — the hero photograph drifts the same way;                */
/*   3. a lighter cut of the same letterforms follows the pointer      */
/*      across the type, the way a raking light crosses a wall. It is  */
/*      masked to a soft pool, fades out when the cursor leaves, and   */
/*      is never mounted on a touch device.                            */
/*                                                                     */
/* All decoration: the glyph carries aria-hidden and the footer's      */
/* accessible name comes from the lockup below.                        */
/* ------------------------------------------------------------------ */

/* Wide and shallow. The pool has to graze a band of type around 160px tall
   and 1300 across, so a circle would either miss the letters or wash the
   whole panel at once. */
const GRAZE_MASK =
    'radial-gradient(32% 150% at var(--graze-x, 50%) var(--graze-y, 50%), #000 0%, rgb(0 0 0 / 0.4) 46%, transparent 78%)';

function FooterWordmark() {
    const panel = useRef(null);
    const plate = useRef(null);
    const reduced = useReducedMotion();
    const fine = useMediaQuery('(hover: hover) and (pointer: fine)');
    const graze = fine && !reduced;

    // The band sits at the very bottom of the document, so the range never
    // completes. What gets used is the middle of it, which is the part where
    // the drift is slowest — the reason the two ends are set this wide.
    const { scrollYProgress } = useScroll({ target: panel, offset: ['start end', 'end start'] });
    const y = useTransform(scrollYProgress, [0, 1], ['8%', '-8%']);

    useEffect(() => {
        const node = plate.current;
        if (!node || !graze) return undefined;

        let frame;

        // The pointer is measured against the type's own box rather than the
        // padded band, so the light lands under the cursor rather than a
        // little above it.
        const move = (event) => {
            const rect = node.getBoundingClientRect();

            cancelAnimationFrame(frame);
            frame = requestAnimationFrame(() => {
                const x = ((event.clientX - rect.left) / rect.width) * 100;
                const top = ((event.clientY - rect.top) / rect.height) * 100;

                node.style.setProperty('--graze-x', x + '%');
                node.style.setProperty('--graze-y', top + '%');
                node.style.setProperty('--graze-opacity', '1');
            });
        };

        const leave = () => {
            cancelAnimationFrame(frame);
            node.style.setProperty('--graze-opacity', '0');
        };

        node.addEventListener('pointermove', move);
        node.addEventListener('pointerleave', leave);

        return () => {
            cancelAnimationFrame(frame);
            node.removeEventListener('pointermove', move);
            node.removeEventListener('pointerleave', leave);
        };
    }, [graze]);

    const type = (
        <div ref={plate} className="relative">
            <LogoWordmark className="block w-full select-none text-ink-800" />

            {graze && (
                <div
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 transition-opacity duration-700 [transition-timing-function:var(--ease-out-expo)]"
                    style={{
                        opacity: 'var(--graze-opacity, 0)',
                        WebkitMaskImage: GRAZE_MASK,
                        maskImage: GRAZE_MASK,
                    }}
                >
                    <LogoWordmark className="block h-full w-full select-none text-ink-600" />
                </div>
            )}
        </div>
    );

    if (reduced) {
        return <div className="shell py-12 sm:py-14">{type}</div>;
    }

    return (
        <motion.div
            ref={panel}
            style={{ y }}
            className="shell py-12 sm:py-14"
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true, amount: 0.35 }}
            variants={{ hidden: {}, visible: {} }}
        >
            {/* The rise is watched from this wrapper rather than from the
                translated child: Chrome folds a transform into the rectangle it
                hands IntersectionObserver, and a child parked a full height
                below its box can report itself out of view and never start. */}
            <span className="block overflow-hidden">
                <motion.span
                    className="block"
                    variants={{
                        hidden: { y: '104%' },
                        // Slower than the headings. The further a thing has to
                        // travel the longer it needs, or the move reads as a snap.
                        visible: { y: '0%', transition: { duration: 1.5, ease: EASE.outExpo } },
                    }}
                >
                    {type}
                </motion.span>
            </span>
        </motion.div>
    );
}

export function Footer() {
    const { t, site } = useSite();
    const year = new Date().getFullYear();

    return (
        <footer className="noise-overlay relative overflow-hidden bg-ink-950 text-concrete-300">
            <div className="blueprint-grid absolute inset-0 opacity-50" />

            {/* Oversized wordmark that anchors the page */}
            <div className="relative overflow-hidden border-b border-ink-800">
                <FooterWordmark />
            </div>

            <div className="shell relative py-12 sm:py-14">
                <div className="grid gap-10 lg:grid-cols-12">
                    <div className="lg:col-span-4">
                        {/* The line below carries the slogan at a readable
                            size, so the lockup does not repeat it. It takes the
                            same display italic as the header's, one step up in
                            scale, so the brand voice reads the same in both. */}
                        <BrandLogo tone="light" tagline={false} />

                        <p className="mt-6 max-w-sm font-display text-base font-bold italic uppercase leading-snug tracking-[0.06em] text-concrete-300">
                            <SplitText text={site.brand?.tagline ?? ''} stagger={0.055} />
                        </p>

                        <address className="mt-8 space-y-3 not-italic text-sm">
                            <a
                                href={site.contact?.maps_url}
                                target="_blank"
                                rel="noreferrer"
                                className="flex items-start gap-3 text-concrete-300 transition-colors hover:text-brand"
                            >
                                <Icon name="map-pin" className="mt-0.5 size-4 shrink-0 text-brand" />
                                <span>
                                    {site.contact?.address}
                                    <br />
                                    {site.contact?.district}, {site.contact?.city}
                                    <br />
                                    {site.contact?.postal_box}
                                </span>
                            </a>
                            <a
                                href={`tel:${site.contact?.phone?.replace(/\s/g, '')}`}
                                className="flex items-center gap-3 text-concrete-300 transition-colors hover:text-brand"
                            >
                                <Icon name="phone" className="size-4 shrink-0 text-brand" />
                                {site.contact?.phone}
                            </a>
                            <a
                                href={`mailto:${site.contact?.email}`}
                                className="flex items-center gap-3 text-concrete-300 transition-colors hover:text-brand"
                            >
                                <Icon name="mail" className="size-4 shrink-0 text-brand" />
                                {site.contact?.email}
                            </a>
                            <p className="flex items-center gap-3 text-concrete-500">
                                <Icon name="clock" className="size-4 shrink-0 text-brand" />
                                {site.contact?.hours}
                            </p>
                        </address>

                        <div className="mt-8 flex gap-3">
                            {Object.entries(site.social ?? {}).map(([key, href]) => (
                                <a
                                    key={key}
                                    href={href}
                                    target="_blank"
                                    rel="noreferrer"
                                    aria-label={key}
                                    className="flex size-10 items-center justify-center rounded-full border border-ink-700 text-concrete-400 transition-all duration-400 hover:border-brand hover:bg-brand hover:text-ink-950"
                                >
                                    <Icon name={SOCIAL_ICONS[key] ?? 'link'} className="size-4" />
                                </a>
                            ))}
                        </div>
                    </div>

                    <div className="lg:col-span-2">
                        <h2 className="eyebrow text-concrete-500">{t('footer.company')}</h2>
                        <ul className="mt-6 space-y-3 text-sm">
                            {FOOTER_NAV.company.map((item) => (
                                <li key={item.href}>
                                    <Link
                                        href={item.href}
                                        className="link-draw text-concrete-300 transition-colors hover:text-brand"
                                    >
                                        {t(item.key)}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>

                    <div className="lg:col-span-3">
                        <h2 className="eyebrow text-concrete-500">{t('footer.expertise')}</h2>
                        <ul className="mt-6 space-y-3 text-sm">
                            {FOOTER_NAV.expertise.map((item) => (
                                <li key={item.href}>
                                    <Link
                                        href={item.href}
                                        className="link-draw text-concrete-300 transition-colors hover:text-brand"
                                    >
                                        {item.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>

                    <div className="lg:col-span-3">
                        <h2 className="eyebrow text-concrete-500">{t('footer.newsletter')}</h2>
                        <p className="mt-6 text-sm leading-relaxed text-concrete-400">
                            {t('blog.newsletter_lead')}
                        </p>
                        <Newsletter />

                        <dl className="mt-10 space-y-2 font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-600">
                            <div className="flex gap-2">
                                <dt>{t('footer.rccm')}</dt>
                                <dd className="text-concrete-500">{site.legal?.rccm}</dd>
                            </div>
                            <div className="flex gap-2">
                                <dt>{t('footer.niu')}</dt>
                                <dd className="text-concrete-500">{site.legal?.niu}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div className="relative border-t border-ink-800">
                <div className="shell flex flex-col gap-4 py-7 font-mono text-[0.625rem] uppercase tracking-[0.14em] text-concrete-600 sm:flex-row sm:items-center sm:justify-between">
                    <p>
                        © {year} {site.brand?.legal_name}. {t('footer.rights')}
                    </p>
                    <div className="flex flex-wrap gap-x-6 gap-y-2">
                        <Link href="/mentions-legales" className="transition-colors hover:text-brand">
                            {t('footer.legal')}
                        </Link>
                        <Link href="/confidentialite" className="transition-colors hover:text-brand">
                            {t('footer.privacy')}
                        </Link>
                        <span className="text-concrete-700">{t('footer.built_in')}</span>
                    </div>
                </div>
            </div>
        </footer>
    );
}
