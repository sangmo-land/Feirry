import { useEffect, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { A, Dialog, Icon, ThemeProvider, useTheme, useV7, waLink } from './core';
import { CDP } from './cdp-i18n';
import { siWhatsapp } from 'simple-icons';

const NAV = [
    ['/', 'home'],
    ['/feirry', 'feirry'],
    ['/expertises', 'expertises'],
    ['/projets', 'projets'],
    ['/offres', 'offres'],
    ['/coup-de-pouce', 'cdp'],
    ['/conseils', 'conseils'],
    ['/mon-projet', 'contact'],
];

function useActive() {
    const { url } = usePage();
    const { path } = useV7();
    const current = url.split(/[?#]/)[0];
    return (href) => {
        const target = path(href);
        if (href === '/') return current === target;
        return current === target || current.startsWith(`${target}/`);
    };
}

function Logo({ height = 30, className = 'f-logo' }) {
    return (
        <>
            <img className={`${className} logo-on-dark`} src="/images/brand/feirry-logo-white-64.png" alt="FEIRRY Construction Designs" width={Math.round((height * 962) / 158)} height={height} style={{ height, width: 'auto' }} />
            <img className={`${className} logo-on-light`} src="/images/brand/feirry-logo-black-64.png" alt="FEIRRY Construction Designs" width={Math.round((height * 962) / 158)} height={height} style={{ height, width: 'auto' }} />
        </>
    );
}

function LangSwitch() {
    const { L, locale, path, url } = useV7();
    const current = url;
    const go = (loc) => {
        try {
            localStorage.setItem('feirry-lang', loc);
        } catch (e) {
            /* ignore */
        }
    };
    const btn = (loc) => {
        const on = locale === loc;
        return (
            <Link
                href={path(current, loc)}
                hrefLang={loc}
                lang={loc}
                onClick={() => go(loc)}
                aria-current={on ? 'true' : undefined}
                aria-label={loc === 'fr' ? 'FR – Français' : 'EN – English'}
                style={{ width: 44, height: 40, borderRadius: 999, display: 'flex', alignItems: 'center', justifyContent: 'center', font: '700 13px Manrope, sans-serif', background: on ? '#FE5000' : 'transparent', color: on ? '#0B090B' : 'var(--v-text)' }}
            >
                {loc.toUpperCase()}
            </Link>
        );
    };
    return (
        <div role="group" aria-label={L.langAria} className="f-lang" style={{ display: 'flex', padding: 2, borderRadius: 999, border: '1px solid var(--v-line)' }}>
            {btn('fr')}
            {btn('en')}
        </div>
    );
}

function ThemeToggle() {
    const { L } = useV7();
    const { theme, toggle } = useTheme();
    return (
        <button type="button" className="f-iconbtn" onClick={toggle} aria-label={theme === 'dark' ? L.themeToLight : L.themeToDark} title={theme === 'dark' ? L.themeToLight : L.themeToDark}>
            <Icon name={theme === 'dark' ? 'sun' : 'moon'} size={18} />
        </button>
    );
}

function Header({ onMenu, menuOpen }) {
    const { L } = useV7();
    const active = useActive();
    return (
        <header style={{ position: 'sticky', top: 0, zIndex: 40, background: 'var(--v-bg)', borderBottom: '1px solid var(--v-line)' }}>
            <div className="f-wrap f-headrow" style={{ display: 'flex', alignItems: 'center', gap: 20, height: 72 }}>
                <A href="/" aria-label={L.homeAria} style={{ display: 'flex', alignItems: 'center', flex: 'none' }}>
                    <Logo />
                </A>
                <nav className="only-desk f-nav" aria-label={L.navAria} style={{ display: 'flex', gap: 22, flex: 1, justifyContent: 'center', fontSize: 15, fontWeight: 600 }}>
                    {NAV.map(([href, key]) => (
                        <A key={key} href={href} aria-current={active(href) ? 'page' : undefined}>
                            {L.nav[key]}
                        </A>
                    ))}
                </nav>
                <div className="f-ctrls" style={{ display: 'flex', alignItems: 'center', gap: 8, marginLeft: 'auto' }}>
                    <LangSwitch />
                    <ThemeToggle />
                    <A className="f-btn f-btn-sm f-btn-primary hide-mob" href="/mon-projet">
                        {L.ctaHeader}
                    </A>
                    <button type="button" className="f-iconbtn only-tab hide-mob" onClick={onMenu} aria-label={L.menu} aria-expanded={menuOpen} aria-controls="v7-menu">
                        <Icon name="burger" />
                    </button>
                </div>
            </div>
        </header>
    );
}

function Drawer({ open, onClose }) {
    const { L, site } = useV7();
    const active = useActive();
    return (
        <Dialog open={open} onClose={onClose} label={L.menu} className="f-drawer" panelClass="">
            <div id="v7-menu">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', height: 52 }}>
                    <Logo height={26} className="" />
                    <button type="button" className="f-iconbtn" onClick={onClose} aria-label={L.close}>
                        <Icon name="close" size={18} />
                    </button>
                </div>
                <nav aria-label={L.navAria} style={{ display: 'flex', flexDirection: 'column', marginTop: 16 }}>
                    {NAV.map(([href, key]) => (
                        <A
                            key={key}
                            href={href}
                            onClick={onClose}
                            aria-current={active(href) ? 'page' : undefined}
                            style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', minHeight: 56, borderBottom: '1px solid var(--v-line)', color: active(href) ? '#FE5000' : 'var(--v-text)', fontFamily: "'Clash Display', sans-serif", fontSize: 22, fontWeight: 600 }}
                        >
                            {L.nav[key]}
                            <span aria-hidden="true" style={{ color: 'var(--v-accent-text)' }}>
                                →
                            </span>
                        </A>
                    ))}
                </nav>
                <A className="f-btn f-btn-primary" href="/mon-projet" onClick={onClose} style={{ marginTop: 24 }}>
                    {L.ctaPrimary}
                </A>
                <a className="f-btn f-btn-ghost" href={waLink(site)} style={{ marginTop: 10 }}>
                    WhatsApp
                </a>
            </div>
        </Dialog>
    );
}

function Footer() {
    const { L, site, locale } = useV7();
    const { cdpEdition } = usePage().props;
    const c = site.contact ?? {};
    const legal = site.legal ?? {};
    const year = new Date().getFullYear();
    return (
        <footer style={{ borderTop: '1px solid var(--v-line)' }}>
            <div className="f-wrap g4" style={{ paddingTop: 52, paddingBottom: 36, gap: 28, fontSize: 14, color: 'var(--v-muted)', lineHeight: 1.8 }}>
                <div>
                    <Logo height={28} className="" />
                    <p style={{ margin: '14px 0 0', letterSpacing: '.16em', fontSize: 12, fontWeight: 700, color: 'var(--v-text)' }}>SHAPING A BRIGHTER TOMORROW</p>
                    {/* Only the networks FEIRRY has filled in (admin › Paramètres du site). Names, no third-party logos. */}
                    {SOCIAL.some(([k]) => site.social?.[k]) && (
                        <nav aria-label={L.ft.social} style={{ display: 'flex', flexWrap: 'wrap', gap: 8, marginTop: 16 }}>
                            {SOCIAL.filter(([k]) => site.social?.[k]).map(([k, name]) => (
                                <a key={k} href={site.social[k]} target="_blank" rel="noopener noreferrer" className="f-chip" style={{ display: 'inline-flex', alignItems: 'center', textDecoration: 'none', color: 'var(--v-text)' }}>
                                    {name}
                                    <span className="sr-only"> {L.ft.newTab}</span>
                                </a>
                            ))}
                        </nav>
                    )}
                </div>
                <div>
                    <h2 style={{ color: 'var(--v-text)', fontWeight: 700, margin: '0 0 6px', fontSize: 14, fontFamily: 'Manrope, sans-serif' }}>{L.ft.office}</h2>
                    {L.ft.office1}
                    <br />
                    {L.ft.office2}
                    {c.hours && String(c.hours).split(' · ').map((line) => (
                        <span key={line} style={{ display: 'block' }}>{line}</span>
                    ))}
                </div>
                <div>
                    <h2 style={{ color: 'var(--v-text)', fontWeight: 700, margin: '0 0 6px', fontSize: 14, fontFamily: 'Manrope, sans-serif' }}>Contact</h2>
                    {c.phone && (
                        <a href={`tel:${c.phone.replace(/\s/g, '')}`} style={{ color: 'var(--v-muted)' }}>
                            {c.phone_label ? `${c.phone_label} ` : ''}
                            {c.phone}
                        </a>
                    )}
                    <br />
                    {c.phone_secondary && (
                        <a href={`tel:${c.phone_secondary.replace(/\s/g, '')}`} style={{ color: 'var(--v-muted)' }}>
                            {c.phone_secondary_label ? `${c.phone_secondary_label} ` : ''}
                            {c.phone_secondary}
                        </a>
                    )}
                    <br />
                    {c.email && (
                        <a href={`mailto:${c.email}`} style={{ color: 'var(--v-muted)' }}>
                            {c.email}
                        </a>
                    )}
                </div>
                <div>
                    <h2 style={{ color: 'var(--v-text)', fontWeight: 700, margin: '0 0 6px', fontSize: 14, fontFamily: 'Manrope, sans-serif' }}>{L.ft.company}</h2>
                    {legal.rccm && <>{legal.rccm}<br /></>}
                    {legal.niu && <>NIU {legal.niu}<br /></>}
                    <A href="/coup-de-pouce" style={{ color: 'var(--v-text)', fontWeight: 700 }}>{CDP[locale]?.footer ?? CDP.fr.footer}</A>
                    {cdpEdition && <span style={{ display: 'block', fontSize: 13 }}>{cdpEdition}</span>}
                    <A href="/mentions-legales" style={{ color: 'var(--v-muted)' }}>{L.ft.legal}</A>
                    {' · '}
                    <A href="/confidentialite" style={{ color: 'var(--v-muted)' }}>{L.ft.privacy}</A>
                </div>
            </div>
            <div className="f-wrap" style={{ paddingTop: 18, paddingBottom: 28, borderTop: '1px solid var(--v-line)', fontSize: 13, color: 'var(--v-muted)' }}>
                © {year} FEIRRY Construction Designs · {L.ft.note}
            </div>
            <div className="only-mob f-tabspace" />
        </footer>
    );
}

function TabBar({ onMenu, menuOpen }) {
    const { L } = useV7();
    const active = useActive();
    const tabs = [
        ['/', 'home', L.nav.home],
        ['/projets', 'grid', L.nav.projets],
        ['/offres', 'tag', L.nav.offres],
    ];
    return (
        <nav className="only-mob f-tabbar" aria-label={L.tabAria} style={{ position: 'fixed', left: 0, right: 0, bottom: 0, zIndex: 35, background: 'var(--v-bg)', borderTop: '1px solid var(--v-line)', alignItems: 'center', gap: 2, boxSizing: 'border-box' }}>
            {tabs.map(([href, icon, label]) => (
                <A key={href} href={href} aria-current={active(href) ? 'page' : undefined} style={{ flex: 1, minHeight: 52, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: 3, color: active(href) ? 'var(--v-accent-text)' : 'var(--v-muted)', fontSize: 11, fontWeight: 700 }}>
                    <Icon name={icon} size={21} />
                    {label}
                </A>
            ))}
            <button type="button" onClick={onMenu} aria-label={L.menu} aria-expanded={menuOpen} aria-controls="v7-menu" style={{ flex: 1, minHeight: 52, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: 3, border: 0, background: 'transparent', color: menuOpen ? 'var(--v-accent-text)' : 'var(--v-muted)', font: '700 11px Manrope, sans-serif', cursor: 'pointer' }}>
                <Icon name="burger" size={21} />
                {L.menu}
            </button>
            <A href="/mon-projet" aria-current={active('/mon-projet') ? 'page' : undefined} style={{ flex: 'none', display: 'flex', alignItems: 'center', height: 44, padding: '0 18px', borderRadius: 999, background: '#FE5000', color: '#0B090B', fontSize: 14, fontWeight: 800 }}>
                {L.tabCta}
            </A>
        </nav>
    );
}

function Shell({ children }) {
    const { L, locale, site, url } = useV7();
    const { props } = usePage();
    const [menu, setMenu] = useState(false);

    useEffect(() => {
        document.documentElement.lang = locale;
        document.body.classList.add('v7-body');
    }, [locale]);

    // Close the menu on any navigation.
    useEffect(() => router.on('navigate', () => setMenu(false)), []);

    // After an in-page anchor navigation (e.g. /#contact), move focus there.
    useEffect(() => {
        const hash = window.location.hash.slice(1);
        if (!hash) return;
        const target = document.getElementById(hash);
        if (target) window.setTimeout(() => target.scrollIntoView({ block: 'start' }), 60);
    }, [url]);

    return (
        <div className="v7">
            <Head title={props.seo?.full_title} />
            <a className="skip" href="#contenu">
                {L.skip}
            </a>
            <Header onMenu={() => setMenu(true)} menuOpen={menu} />
            <Drawer open={menu} onClose={() => setMenu(false)} />
            <main id="contenu" tabIndex={-1} style={{ outline: 'none' }}>
                {children}
            </main>
            <Footer />
            <aside aria-label="WhatsApp">
                <a className="hide-mob" href={waLink(site)} aria-label={L.waAria} style={{ position: 'fixed', right: 24, bottom: 24, zIndex: 30, width: 60, height: 60, borderRadius: 999, background: '#FE5000', color: '#0B090B', display: 'flex', alignItems: 'center', justifyContent: 'center', boxShadow: '0 12px 30px rgba(254,80,0,.35)' }}>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d={siWhatsapp.path} />
                    </svg>
                </a>
            </aside>
            <TabBar onMenu={() => setMenu(true)} menuOpen={menu} />
        </div>
    );
}

const SOCIAL = [['facebook', 'Facebook'], ['instagram', 'Instagram'], ['tiktok', 'TikTok'], ['linkedin', 'LinkedIn'], ['youtube', 'YouTube']];

export default function V7Layout({ children }) {
    return (
        <ThemeProvider>
            <Shell>{children}</Shell>
        </ThemeProvider>
    );
}
