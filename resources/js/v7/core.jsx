import { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from 'react';
import { Link as InertiaLink, usePage } from '@inertiajs/react';
import { localizePath } from '@/Lib/routes';
import { dict } from './i18n';

/* ------------------------------------------------------------------ */
/* Context: locale, strings, localised paths, theme                    */
/* ------------------------------------------------------------------ */

export function useV7() {
    const { props, url } = usePage();
    const locale = props.locale ?? 'fr';
    const config = useMemo(
        () => ({ segments: props.segments ?? {}, locales: props.locales ?? {}, defaultLocale: props.defaultLocale ?? 'fr' }),
        [props.segments, props.locales, props.defaultLocale],
    );
    const path = useCallback((href, loc = locale) => localizePath(href, loc, config), [locale, config]);

    return { L: dict(locale), locale, fr: locale === 'fr', path, url, site: props.site ?? {}, flash: props.flash ?? {}, config };
}

const ThemeContext = createContext({ theme: 'dark', toggle: () => {} });

export function ThemeProvider({ children }) {
    const [theme, setTheme] = useState(() =>
        typeof document !== 'undefined' ? document.documentElement.dataset.theme || 'dark' : 'dark',
    );

    const toggle = useCallback(() => {
        setTheme((current) => {
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = next;
            document.querySelector('meta[name="theme-color"]')?.setAttribute('content', next === 'dark' ? '#0b090b' : '#faf7f2');
            try {
                localStorage.setItem('feirry-theme', next);
            } catch (e) {
                /* private mode: the choice still applies to this visit */
            }
            return next;
        });
    }, []);

    return <ThemeContext.Provider value={{ theme, toggle }}>{children}</ThemeContext.Provider>;
}

export const useTheme = () => useContext(ThemeContext);

/** Internal link, written in its French form and rendered in the active language. */
export function A({ href, children, ...rest }) {
    const { path } = useV7();
    if (!href || /^(https?:|mailto:|tel:|#)/.test(href)) {
        return (
            <a href={href} {...rest}>
                {children}
            </a>
        );
    }
    return (
        <InertiaLink href={path(href)} {...rest}>
            {children}
        </InertiaLink>
    );
}

/* ------------------------------------------------------------------ */
/* Icons                                                               */
/* ------------------------------------------------------------------ */

const PATHS = {
    home: 'M3 11l9-7 9 7M5 10v10h14V10', grid: 'M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z',
    tag: 'M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8zM7.5 7.5h.01',
    cal: 'M4 5h16v16H4zM4 10h16M9 3v4M15 3v4', pin: 'M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21zM12 12a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z',
    route: 'M5 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM19 9a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM7 17h6a4 4 0 0 0 0-8h-2a4 4 0 0 1 0-8h6',
    cube: 'M12 2l9 5v10l-9 5-9-5V7zM12 12l9-5M12 12v10M12 12L3 7',
    map: 'M9 4L3 6v14l6-2 6 2 6-2V4l-6 2zM9 4v14M15 6v14', level: 'M3 20h18M6 16l4-8 4 5 3-3 4 6', drill: 'M12 2v14M8 12l4 4 4-4M5 20h14',
    user: 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21a8 8 0 0 1 16 0', eye: 'M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12zM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z',
    doc: 'M7 3h7l5 5v13H7zM14 3v5h5M10 13h6M10 17h6', ruler: 'M3 17L17 3l4 4L7 21zM7 13l2 2M10 10l2 2M13 7l2 2',
    sun: 'M12 4V2M12 22v-2M4 12H2M22 12h-2M5.6 5.6L4.2 4.2M19.8 19.8l-1.4-1.4M5.6 18.4l-1.4 1.4M19.8 4.2l-1.4 1.4M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10z',
    moon: 'M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z',
    arrow: 'M5 12h14M13 6l6 6-6 6', arrowL: 'M19 12H5M11 6l-6 6 6 6', check: 'M5 12l5 5L20 7', close: 'M6 6l12 12M18 6L6 18',
    burger: 'M4 7h16M4 12h16M4 17h16', up: 'M6 15l6-6 6 6', down: 'M6 9l6 6 6-6', upload: 'M12 16V4M7 9l5-5 5 5M4 20h16',
    lock: 'M6 11h12v10H6zM8 11V7a4 4 0 0 1 8 0v4', chat: 'M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 21l2.1-5.4A8.4 8.4 0 1 1 21 11.5z',
    phone: 'M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 0-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z', mail: 'M3 5h18v14H3zM3 6l9 7 9-7',
    minus: 'M5 12h14', file: 'M7 3h7l5 5v13H7zM14 3v5h5',
};

export function Icon({ name, size = 20, stroke = 2, color = 'currentColor', style }) {
    return (
        <svg aria-hidden="true" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth={stroke} strokeLinecap="round" strokeLinejoin="round" style={{ flex: 'none', ...style }}>
            <path d={PATHS[name] ?? name} />
        </svg>
    );
}

/* ------------------------------------------------------------------ */
/* Media                                                               */
/* ------------------------------------------------------------------ */

/** Responsive variants are stored as NAME-640/1280/1920.webp next to the 1280 one. */
export function srcSet(src) {
    if (!src || !/-1280\.webp$/.test(src)) return undefined;
    return [320, 640, 960, 1280, 1920].map((w) => `${src.replace(/-1280\.webp$/, `-${w}.webp`)} ${w}w`).join(', ');
}

/** ResponsiveImage: reserved ratio from its container, lazy unless `priority`. */
export function Img({ src, alt = '', pos = 'center 50%', sizes = '(max-width: 767px) 100vw, 50vw', priority = false, className = 'f-img', style }) {
    if (!src) return null;
    return (
        <img
            className={className}
            src={src}
            srcSet={srcSet(src)}
            sizes={srcSet(src) ? sizes : undefined}
            alt={alt}
            loading={priority ? 'eager' : 'lazy'}
            fetchPriority={priority ? 'high' : undefined}
            decoding="async"
            style={{ objectPosition: pos, ...style }}
        />
    );
}

/** MediaFrame: fixed-height (or ratio) box that never collapses while the image loads. */
export function Media({ src, alt, pos, height, ratio, sizes, priority, provisional, children, radius, className = '', style }) {
    const { L } = useV7();
    return (
        <div className={`f-media ${className}`} style={{ height, aspectRatio: ratio, borderRadius: radius, ...style }}>
            <Img src={src} alt={alt} pos={pos} sizes={sizes} priority={priority} />
            {provisional && (
                <span className="f-pill" style={{ top: 16, right: 16, fontSize: 11 }}>
                    {L.provisional}
                </span>
            )}
            {children}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* Dialog: focus trap, Escape, focus restored, background locked       */
/* ------------------------------------------------------------------ */

export function Dialog({ open, onClose, label, children, width = 760, className = 'f-modal', panelClass = 'f-dialog' }) {
    const panel = useRef(null);
    const opener = useRef(null);

    useEffect(() => {
        if (!open) return undefined;
        opener.current = document.activeElement;
        const html = document.documentElement;
        const prev = html.style.overflow;
        html.style.overflow = 'hidden';

        const focusables = () => panel.current?.querySelectorAll('a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])') ?? [];
        window.setTimeout(() => (focusables()[0] ?? panel.current)?.focus(), 30);

        const onKey = (e) => {
            if (e.key === 'Escape') {
                e.preventDefault();
                onClose();
            }
            if (e.key === 'Tab') {
                const items = Array.from(focusables());
                if (!items.length) return;
                const first = items[0];
                const last = items[items.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        };
        document.addEventListener('keydown', onKey);
        return () => {
            document.removeEventListener('keydown', onKey);
            html.style.overflow = prev;
            opener.current?.focus?.();
        };
    }, [open, onClose]);

    if (!open) return null;

    return (
        <div className={className} onMouseDown={(e) => e.target === e.currentTarget && onClose()}>
            <div ref={panel} role="dialog" aria-modal="true" aria-label={label} tabIndex={-1} className={panelClass} style={panelClass === 'f-dialog' ? { width: `min(${width}px, 100%)` } : undefined}>
                {children}
            </div>
        </div>
    );
}

export function CloseButton({ onClick, label, overlay = false }) {
    return (
        <button
            type="button"
            onClick={onClick}
            aria-label={label}
            className={overlay ? undefined : 'f-iconbtn'}
            style={overlay ? { position: 'absolute', top: 14, right: 14, width: 44, height: 44, borderRadius: 999, border: 0, background: 'rgba(11,9,11,.75)', color: '#F7F3EE', cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 2 } : undefined}
        >
            <Icon name="close" size={18} />
        </button>
    );
}

/* ------------------------------------------------------------------ */
/* Small shared pieces                                                 */
/* ------------------------------------------------------------------ */

export function Check({ size = 18, top = 3 }) {
    return <Icon name="check" size={size} stroke={2.4} color="#FE5000" style={{ marginTop: top }} />;
}

export function CheckList({ items, gap = 10, size = 15 }) {
    return (
        <ul className="f-list" style={{ gap, fontSize: size }}>
            {items.map((it) => (
                <li key={it}>
                    <Check />
                    {it}
                </li>
            ))}
        </ul>
    );
}

export function SectionHead({ eye, title, lead, id, center = false, titleMax = 720, leadMax = 400, as: H = 'h2' }) {
    if (center) {
        return (
            <div style={{ textAlign: 'center', marginBottom: 44 }}>
                <div className="f-eye">{eye}</div>
                <H className="f-h2" id={id} style={{ maxWidth: titleMax, marginLeft: 'auto', marginRight: 'auto' }}>
                    {title}
                </H>
                {lead && (
                    <p className="f-lead" style={{ margin: '20px auto 0', maxWidth: 600 }}>
                        {lead}
                    </p>
                )}
            </div>
        );
    }
    return (
        <div className="f-head">
            <div>
                <div className="f-eye">{eye}</div>
                <H className="f-h2" id={id} style={{ maxWidth: titleMax }}>
                    {title}
                </H>
            </div>
            {lead && (
                <p className="f-lead" style={{ maxWidth: leadMax }}>
                    {lead}
                </p>
            )}
        </div>
    );
}

export function RoleBadges({ roles = [] }) {
    const { L } = useV7();
    if (!roles.length) return <span className="f-badge tbc">{L.missionTbcBadge}</span>;
    return roles.map((r) => (
        <span key={r} className={`f-badge ${r}`}>
            {(L.roleName[r] ?? r).toUpperCase()}
        </span>
    ));
}

export function rolesText(L, roles = []) {
    return roles.length ? roles.map((r) => L.roleName[r] ?? r).join(' · ') : L.missionTbc;
}

export function stageText(L, p) {
    if (!p.stage || p.stage === 'tbc') return L.tbc;
    return L.stageName[p.stage] + (p.stage_note ? ` — ${p.stage_note}` : '');
}

export function Crumbs({ items }) {
    const { L } = useV7();
    return (
        <nav aria-label={L.crumbs} className="f-crumbs">
            <A href="/">{L.nav.home}</A>
            {items.map(([label, href]) => (
                <span key={label} style={{ display: 'flex', gap: 8 }}>
                    <span aria-hidden="true">/</span>
                    {href ? <A href={href}>{label}</A> : <span aria-current="page" style={{ color: 'var(--v-text)' }}>{label}</span>}
                </span>
            ))}
        </nav>
    );
}

export function PageHead({ crumbs, eye, title, lead, children }) {
    return (
        <header className="f-wrap f-pagehead">
            <Crumbs items={crumbs} />
            <div className="f-eye">{eye}</div>
            <h1 className="f-h2" style={{ maxWidth: 900, fontSize: 'clamp(34px, 6vw, 64px)' }}>
                {title}
            </h1>
            {lead && (
                <p className="f-lead" style={{ marginTop: 20, maxWidth: 680 }}>
                    {lead}
                </p>
            )}
            {children}
        </header>
    );
}

export function formatDate(iso, locale) {
    if (!iso) return '';
    try {
        return new Date(iso).toLocaleDateString(locale === 'fr' ? 'fr-FR' : 'en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
    } catch (e) {
        return iso;
    }
}

export function waLink(site) {
    return `https://wa.me/${site?.contact?.whatsapp || '237670729579'}`;
}
