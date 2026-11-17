import { useEffect, useMemo, useRef, useState } from 'react';
import { router } from '@inertiajs/react';
import { A, Icon, formatDate, useV7, waLink } from './core';
import { CDP, track } from './cdp-i18n';
import { Banner, Choices, Field, Input, UploadZone } from './forms';

export function useCdp() {
    const v = useV7();
    return { ...v, C: CDP[v.locale] ?? CDP.fr };
}

const digits = (v) => (v || '').replace(/\D/g, '').length;
const emailOk = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v || '');
const newKey = () => (window.crypto?.randomUUID ? window.crypto.randomUUID() : `${Date.now()}-${Math.random().toString(36).slice(2)}-${Math.random().toString(36).slice(2)}`);

/* ------------------------------------------------------------------ */
/* State badge + edition line                                          */
/* ------------------------------------------------------------------ */

export function StateBadge({ campaign }) {
    const { C } = useCdp();
    const live = campaign.state === 'open' || campaign.state === 'waitlist';
    return (
        <span style={{ flex: 'none', padding: '6px 12px', borderRadius: 999, background: live ? '#01B27C' : '#FE5000', color: '#0B090B', fontSize: 12, fontWeight: 800, letterSpacing: '.06em' }}>
            {C.states[campaign.state] ?? C.states.preparation}
        </span>
    );
}

export function StateText({ campaign, style }) {
    const { C, locale } = useCdp();
    const s = campaign.state;
    let text = C.stateText[s] ?? C.stateText.preparation;
    if (s === 'upcoming') text = `${text} ${formatDate(campaign.announced_at ?? campaign.starts_at, locale)}.`;
    const window = (s === 'open' || s === 'waitlist' || s === 'upcoming') && campaign.starts_at && campaign.ends_at ? `${C.editionFrom} ${formatDate(campaign.starts_at, locale)} ${C.to} ${formatDate(campaign.ends_at, locale)}.` : null;
    return (
        <p className="f-muted" style={{ margin: 0, lineHeight: 1.6, ...style }}>
            {campaign.edition && (s === 'open' || s === 'waitlist' || s === 'upcoming') && <strong style={{ color: 'var(--v-text)' }}>{campaign.edition}. </strong>}
            {text} {window}
        </p>
    );
}

function Reassure({ campaign, compact = false }) {
    const { C } = useCdp();
    const places = campaign.public_consultations && campaign.public_visits ? C.placesLabel(campaign.public_consultations, campaign.public_visits) : C.placesFallback;
    const items = [C.reassure[0], [places, C.reassure[1][1]], C.reassure[2]];
    return (
        <ul className={compact ? 'chips' : 'g3'} style={{ listStyle: 'none', margin: 0, padding: 0, gap: compact ? 8 : 16 }}>
            {items.map(([t, d]) =>
                compact ? (
                    <li key={t} style={{ padding: '8px 12px', borderRadius: 999, border: '1px solid var(--v-line-strong)', fontSize: 13, fontWeight: 700 }}>
                        {t}
                    </li>
                ) : (
                    <li key={t} style={{ padding: '16px 18px', borderRadius: 16, border: '1px solid var(--v-line)', background: 'var(--v-surface)' }}>
                        <div style={{ fontWeight: 800 }}>{t}</div>
                        <div className="f-muted" style={{ fontSize: 14, marginTop: 2 }}>{d}</div>
                    </li>
                ),
            )}
        </ul>
    );
}

/* ------------------------------------------------------------------ */
/* Home, section 08: short editorial block (CDC §02)                    */
/* ------------------------------------------------------------------ */

export function CdpHomeBlock({ campaign }) {
    const { C } = useCdp();
    return (
        <section id="coup-de-pouce" className="f-sec f-wrap" aria-labelledby="s-cdp">
            <div className="f-card f-pad" style={{ padding: 48, background: 'var(--v-cdp-bg)', border: '1px solid rgba(254,80,0,.45)' }}>
                <div className="split" style={{ alignItems: 'center' }}>
                    <div>
                        <div className="chips">
                            <StateBadge campaign={campaign} />
                            <span className="f-eye" style={{ flex: 'none', alignSelf: 'center', color: 'var(--v-muted)' }}>{C.eyebrow}</span>
                        </div>
                        <h2 className="f-h2" id="s-cdp" style={{ marginTop: 16 }}>
                            Coup de Pouce <span style={{ color: '#FE5000' }}>FEIRRY</span>
                        </h2>
                        <p style={{ margin: '14px 0 0', fontFamily: "'Clash Display', sans-serif", fontSize: 24, fontWeight: 600, lineHeight: 1.25 }}>{C.promise}</p>
                        <p className="f-lead" style={{ marginTop: 14, maxWidth: 520 }}>{C.homeLead}</p>
                        <StateText campaign={campaign} style={{ marginTop: 14, fontSize: 15 }} />
                        <A className="f-btn f-btn-primary f-btn-inline" href="/coup-de-pouce" style={{ marginTop: 24 }}>
                            {C.discover}
                            <Icon name="arrow" size={18} stroke={2.2} />
                        </A>
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                        {['consultation', 'visit'].map((p) => (
                            <A key={p} href={`/coup-de-pouce/${p === 'visit' ? 'visite' : 'consultation'}`} className="f-card f-hover" onClick={() => track(p === 'visit' ? 'cdp_choose_b' : 'cdp_choose_a')} style={{ display: 'block', padding: '18px 20px', background: 'var(--v-surface)', border: '1px solid var(--v-line)', color: 'var(--v-text)' }}>
                                <span className="f-eye" style={{ fontSize: 12 }}>{C.cards[p].num}</span>
                                <span style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: 12, marginTop: 6 }}>
                                    <span style={{ fontFamily: "'Clash Display', sans-serif", fontWeight: 700, fontSize: 20 }}>{C.cards[p].title}</span>
                                    <Icon name="arrow" size={18} color="#FE5000" />
                                </span>
                                <span className="f-muted" style={{ display: 'block', marginTop: 4, fontSize: 14 }}>{C.cards[p].micro}</span>
                            </A>
                        ))}
                        <Reassure campaign={campaign} compact />
                    </div>
                </div>
            </div>
        </section>
    );
}

/* ------------------------------------------------------------------ */
/* Programme page: promise → two choices → how → terms → FAQ (CDC §03) */
/* ------------------------------------------------------------------ */

function ActionCard({ path, campaign }) {
    const { C } = useCdp();
    const c = C.cards[path];
    const enabled = campaign[path] !== false;
    return (
        <article className="f-card f-panel f-pad" style={{ padding: 32, display: 'flex', flexDirection: 'column', gap: 12 }}>
            <span className="f-eye">{c.num}</span>
            <h2 style={{ margin: 0, fontSize: 28 }}>{c.title}</h2>
            <p className="f-muted" style={{ margin: 0, lineHeight: 1.6 }}>{c.who}</p>
            <p style={{ margin: 0, lineHeight: 1.6 }}>{c.text}</p>
            <div style={{ marginTop: 'auto', paddingTop: 12 }}>
                {campaign.open && enabled ? (
                    <A className="f-btn f-btn-primary" href={`/coup-de-pouce/${path === 'visit' ? 'visite' : 'consultation'}`} onClick={() => track(path === 'visit' ? 'cdp_choose_b' : 'cdp_choose_a')} style={{ width: '100%' }}>
                        {c.cta}
                        <Icon name="arrow" size={18} stroke={2.2} />
                    </A>
                ) : (
                    <span className="f-btn f-btn-ghost" aria-disabled="true" style={{ width: '100%' }}>
                        {c.cta}
                    </span>
                )}
                <p className="f-muted" style={{ margin: '10px 0 0', fontSize: 13 }}>{c.micro}</p>
            </div>
        </article>
    );
}

export function CdpPage({ campaign }) {
    const { C, L, site } = useCdp();
    useEffect(() => track('cdp_view'), []);
    const terms = (campaign.terms || '').split('\n').filter(Boolean);
    const wa = campaign.support_whatsapp ? `https://wa.me/${campaign.support_whatsapp.replace(/\D/g, '')}` : waLink(site);

    return (
        <>
            {/* 01 — Promesse */}
            <header className="f-wrap f-pagehead">
                <nav aria-label={L.crumbs} className="f-crumbs">
                    <A href="/">{L.nav.home}</A>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page" style={{ color: 'var(--v-text)' }}>{L.nav.cdp}</span>
                </nav>
                <div className="f-card f-pad" style={{ padding: 48, background: 'var(--v-cdp-bg)', border: '1px solid rgba(254,80,0,.45)' }}>
                    <div className="chips">
                        <StateBadge campaign={campaign} />
                        <span className="f-eye" style={{ alignSelf: 'center', color: 'var(--v-muted)' }}>{C.name}</span>
                    </div>
                    <h1 className="f-h2" style={{ marginTop: 16, maxWidth: 820, fontSize: 'clamp(34px, 6vw, 64px)' }}>{C.promise}</h1>
                    <p className="f-lead" style={{ marginTop: 18, maxWidth: 660 }}>{C.lead}</p>
                    <StateText campaign={campaign} style={{ marginTop: 14, maxWidth: 660 }} />
                    {campaign.open && (
                        <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap', marginTop: 28 }}>
                            {campaign.consultation && <A className="f-btn f-btn-primary" href="/coup-de-pouce/consultation" onClick={() => track('cdp_choose_a')}>{C.cards.consultation.cta}</A>}
                            {campaign.visit && <A className="f-btn f-btn-ghost" href="/coup-de-pouce/visite" onClick={() => track('cdp_choose_b')}>{C.cards.visit.cta}</A>}
                        </div>
                    )}
                    {!campaign.open && (
                        <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap', marginTop: 28 }}>
                            <A className="f-btn f-btn-primary" href="/mon-projet">{C.form.closedProject}</A>
                            <a className="f-btn f-btn-ghost" href={wa}>WhatsApp</a>
                        </div>
                    )}
                </div>
            </header>

            {/* 02 — Deux choix */}
            <section className="f-sec f-wrap" style={{ paddingTop: 40 }} aria-label={C.cards.consultation.title + ' / ' + C.cards.visit.title}>
                <div className="g2">
                    {campaign.consultation !== false && <ActionCard path="consultation" campaign={campaign} />}
                    {campaign.visit !== false && <ActionCard path="visit" campaign={campaign} />}
                </div>
                <div style={{ marginTop: 16 }}>
                    <Reassure campaign={campaign} />
                </div>
            </section>

            {/* 03 — Fonctionnement */}
            <section className="f-sec f-wrap" aria-labelledby="cdp-how">
                <h2 id="cdp-how" className="f-h2 f-h2s" style={{ marginBottom: 28 }}>{C.howTitle}</h2>
                <ol className="g3" style={{ listStyle: 'none', margin: 0, padding: 0 }}>
                    {C.how.map(([t, d], i) => (
                        <li key={t} className="f-card f-panel" style={{ padding: 28 }}>
                            <div className="f-accent" style={{ fontFamily: "'Clash Display', sans-serif", fontSize: 40, fontWeight: 700, lineHeight: 1 }} aria-hidden="true">{String(i + 1).padStart(2, '0')}</div>
                            <h3 style={{ margin: '14px 0 6px', fontSize: 21 }}>
                                <span className="sr-only">{i + 1}. </span>
                                {t}
                            </h3>
                            <p className="f-muted" style={{ margin: 0, lineHeight: 1.6 }}>{d}</p>
                        </li>
                    ))}
                </ol>
            </section>

            {/* 04 — Conditions · 05 — FAQ */}
            <section className="f-sec f-wrap" style={{ paddingBottom: 112 }}>
                <div className="split" style={{ alignItems: 'start' }}>
                    <div>
                        <h2 className="f-h2 f-h2s">{C.termsTitle}</h2>
                        {terms.length ? (
                            <ul className="f-list" style={{ marginTop: 18, gap: 12 }}>
                                {terms.map((t) => (
                                    <li key={t} style={{ lineHeight: 1.6 }}>
                                        <Icon name="check" size={18} stroke={2.4} color="#FE5000" style={{ marginTop: 3 }} />
                                        {t}
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <p className="f-muted" style={{ marginTop: 18 }}>{C.termsFallback}</p>
                        )}
                        <a className="f-textbtn" href={wa} style={{ marginTop: 18 }}>
                            {C.support}
                            <Icon name="arrow" size={16} stroke={2.2} />
                        </a>
                    </div>
                    <div>
                        <h2 className="f-h2 f-h2s">{C.faqTitle}</h2>
                        <div style={{ marginTop: 18 }}>
                            {C.faq.map(([q, a]) => (
                                <details key={q} style={{ borderTop: '1px solid var(--v-line)', padding: '6px 0' }}>
                                    <summary style={{ cursor: 'pointer', fontWeight: 700, minHeight: 44, display: 'flex', alignItems: 'center' }}>{q}</summary>
                                    <p className="f-muted" style={{ margin: '0 0 12px', lineHeight: 1.6 }}>{a}</p>
                                </details>
                            ))}
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}

/* ------------------------------------------------------------------ */
/* Forms A and B — two screens, then a separate confirmation (CDC §05) */
/* ------------------------------------------------------------------ */

const EMPTY = {
    city: '', project_type: '', progress: '', land_area: '', land_unknown: false, footprint_area: '', footprint_unknown: false, levels: '',
    description: '', site_location: '', gps: '', availability: '', access_constraints: '', danger: false,
    name: '', phone: '', email: '', meeting_mode: '', privacy: false, marketing: false,
};

const STEP1 = ['city', 'project_type', 'progress', 'land_area', 'footprint_area', 'levels', 'description', 'site_location', 'gps', 'availability', 'danger'];

export function CdpForm({ path, campaign, limits }) {
    const { C, L, path: lp } = useCdp();
    const F = C.form;
    const visit = path === 'visit';
    const store = `feirry-cdp-${path}`;
    const top = useRef(null);
    const [step, setStep] = useState(1);
    const [form, setForm] = useState(EMPTY);
    const [key, setKey] = useState('');
    const [files, setFiles] = useState([]);
    const [errors, setErrors] = useState({});
    const [banner, setBanner] = useState('');
    const [sending, setSending] = useState(false);
    const [progress, setProgress] = useState(null);
    const started = useRef(false);

    // Keep the answers for the session only — never the documents (CDC §05).
    useEffect(() => {
        try {
            const saved = JSON.parse(sessionStorage.getItem(store) || 'null');
            if (saved) {
                setForm({ ...EMPTY, ...saved.form, privacy: false });
                setKey(saved.key || newKey());
                return;
            }
        } catch (e) {
            /* ignore */
        }
        setKey(newKey());
    }, [store]);

    useEffect(() => {
        if (!key) return;
        try {
            sessionStorage.setItem(store, JSON.stringify({ form: { ...form, privacy: false }, key }));
        } catch (e) {
            /* storage unavailable */
        }
    }, [form, key, store]);

    const set = (k) => (v) => {
        if (!started.current) {
            started.current = true;
            track('cdp_form_start');
        }
        setForm((f) => ({ ...f, [k]: v }));
        setErrors((e) => {
            const n = { ...e, [k]: undefined };
            if (!Object.values(n).some(Boolean)) setBanner('');
            return n;
        });
    };

    const focusTop = () => window.setTimeout(() => top.current?.focus(), 30);
    // After a failed check, move focus to the first field to fix (CDC §14).
    const focusError = () =>
        window.setTimeout(() => {
            const el = top.current?.closest('form')?.querySelector('[aria-invalid="true"], fieldset[data-invalid] button');
            (el ?? top.current)?.focus();
        }, 30);

    const outOfZone = visit && form.city.trim().length > 2 && !/douala/i.test(`${form.city} ${form.site_location}`);

    const validate1 = () => {
        const e = {};
        if (form.city.trim().length < 2) e.city = F.errRequired;
        if (!form.project_type) e.project_type = F.errRequired;
        if (!form.progress) e.progress = F.errRequired;
        if (form.description.trim().length < 10) e.description = F.errQuestion;
        if (!visit) {
            for (const f of ['land_area', 'footprint_area']) {
                if (!form[f.replace('_area', '_unknown')] && form[f] !== '' && !(Number(String(form[f]).replace(',', '.')) > 0)) e[f] = F.errNumber;
            }
        } else if (form.site_location.trim().length < 3) e.site_location = F.errRequired;
        setErrors(e);
        return !Object.keys(e).length;
    };

    const validate2 = () => {
        const e = {};
        if (form.name.trim().length < 2) e.name = F.errRequired;
        if (digits(form.phone) < 9 || digits(form.phone) > 15) e.phone = F.errPhone;
        if (form.email.trim() && !emailOk(form.email.trim())) e.email = F.errEmail;
        if (!form.privacy) e.privacy = F.errPrivacy;
        setErrors(e);
        return !Object.keys(e).length;
    };

    const next = () => {
        setBanner('');
        if (!validate1()) {
            setBanner(F.errCheck);
            focusError();
            return;
        }
        setStep(2);
        focusTop();
    };

    const submit = () => {
        setBanner('');
        if (!validate2()) {
            setBanner(F.errCheck);
            focusError();
            return;
        }
        if (sending) return;
        setSending(true);
        const area = (v, unknown) => (unknown ? 'inconnu' : String(v).replace(',', '.'));
        const data = {
            path, idempotency_key: key, website: '',
            city: form.city, project_type: form.project_type, progress: form.progress, description: form.description,
            name: form.name, phone: form.phone, email: form.email,
            privacy: 1, marketing: form.marketing ? 1 : 0,
        };
        if (visit) Object.assign(data, { site_location: form.site_location, gps: form.gps, availability: form.availability, access_constraints: form.access_constraints, danger: form.danger ? 1 : 0 });
        else Object.assign(data, { land_area: area(form.land_area, form.land_unknown), footprint_area: area(form.footprint_area, form.footprint_unknown), levels: form.levels, meeting_mode: form.meeting_mode });
        if (!visit && !form.land_unknown && form.land_area === '') delete data.land_area;
        if (!visit && !form.footprint_unknown && form.footprint_area === '') delete data.footprint_area;
        files.forEach((f, i) => (data[`attachments[${i}]`] = f));

        router.post(lp('/coup-de-pouce/demandes'), data, {
            forceFormData: true,
            preserveScroll: true,
            preserveState: true,
            onProgress: (e) => files.length && e?.percentage != null && setProgress(e.percentage),
            onSuccess: (page) => {
                if (page.props.flash?.error === 'campaign_closed') {
                    setBanner(F.errClosed);
                    return;
                }
                // Success leads to the confirmation page (server redirect).
                track('cdp_submit_success');
                try {
                    sessionStorage.removeItem(store);
                } catch (e) {
                    /* ignore */
                }
            },
            onError: (errs) => {
                track('cdp_submit_error');
                const mapped = {};
                Object.entries(errs).forEach(([k, v]) => (mapped[k.startsWith('attachments') ? 'attachments' : k] = v));
                setErrors(mapped);
                setBanner(F.errCheck);
                if (Object.keys(mapped).some((k) => STEP1.includes(k))) setStep(1);
                focusError();
            },
            onNetworkError: () => {
                track('cdp_submit_error');
                setBanner(F.errNetwork);
                return false;
            },
            onHttpException: (r) => {
                track('cdp_submit_error');
                setBanner(r?.status === 429 ? F.errThrottle : F.errServer);
                return false;
            },
            onFinish: () => {
                setSending(false);
                setProgress(null);
            },
        });
    };

    if (!campaign.open || campaign[path] === false) {
        return (
            <div className="f-card f-panel f-pad" role="status" style={{ padding: 32 }}>
                <h2 style={{ margin: 0, fontSize: 24 }}>{F.closedTitle}</h2>
                <p className="f-muted" style={{ margin: '10px 0 20px', lineHeight: 1.6 }}>{F.errClosed}</p>
                <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap' }}>
                    <A className="f-btn f-btn-primary f-btn-inline" href="/coup-de-pouce">{F.closedBack}</A>
                    <A className="f-btn f-btn-ghost f-btn-inline" href="/mon-projet">{F.closedProject}</A>
                </div>
            </div>
        );
    }

    const names = F.steps[path];
    const opts = (o) => Object.entries(o).map(([v, t]) => [v, t]);

    return (
        <div className="f-card f-panel f-pad" style={{ padding: 32 }}>
            <form noValidate onSubmit={(e) => { e.preventDefault(); step === 1 ? next() : submit(); }} aria-labelledby="cdp-step-title">
                <div ref={top} tabIndex={-1} style={{ outline: 'none' }}>
                    <ol aria-label={L.form.progressAria} style={{ display: 'flex', gap: 6, margin: '0 0 10px', padding: 0, listStyle: 'none' }}>
                        {names.map((n, i) => (
                            <li key={n} aria-current={i + 1 === step ? 'step' : undefined} style={{ flex: 1, height: 4, borderRadius: 4, background: i + 1 <= step ? '#FE5000' : 'var(--v-line-strong)' }}>
                                <span className="sr-only">{n}</span>
                            </li>
                        ))}
                    </ol>
                    <div className="f-muted" style={{ display: 'flex', justifyContent: 'space-between', gap: 12, fontSize: 13, fontWeight: 700, marginBottom: 18 }}>
                        <span>{F.stepOf} {step} {F.of} 2</span>
                        <span className="f-accent">{names[step - 1]}</span>
                    </div>
                </div>

                <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                    <h2 id="cdp-step-title" style={{ margin: 0, fontSize: 24 }}>{names[step - 1]}</h2>
                    {campaign.state === 'waitlist' && step === 1 && (
                        <p role="note" style={{ margin: 0, padding: '12px 14px', borderRadius: 14, border: '1px solid var(--v-line-strong)', lineHeight: 1.55 }}>{F.waitlistNote}</p>
                    )}
                    <Banner text={banner} />

                    {step === 1 && (
                        <>
                            <Field id="cdp-city" label={`${F.city} *`} hint={F.cityHint} error={errors.city}>
                                <Input id="cdp-city" value={form.city} onChange={set('city')} error={errors.city} hint={F.cityHint} autoComplete="address-level2" required />
                            </Field>
                            {visit && (
                                <>
                                    <Field id="cdp-place" label={`${F.landmark} *`} hint={F.landmarkHint} error={errors.site_location}>
                                        <Input id="cdp-place" value={form.site_location} onChange={set('site_location')} error={errors.site_location} hint={F.landmarkHint} required />
                                    </Field>
                                    {outOfZone && <p role="status" className="f-muted" style={{ margin: 0, padding: '12px 14px', borderRadius: 14, border: '1px solid var(--v-line-strong)', lineHeight: 1.55 }}>{F.outOfZone}</p>}
                                    <Field id="cdp-gps" label={F.gps} hint={F.gpsHint} error={errors.gps}>
                                        <Input id="cdp-gps" value={form.gps} onChange={set('gps')} error={errors.gps} hint={F.gpsHint} inputMode="decimal" />
                                    </Field>
                                </>
                            )}
                            <Choices label={visit ? `${F.buildingType} *` : `${F.projectType} *`} value={form.project_type} onPick={set('project_type')} error={errors.project_type} options={opts(visit ? F.buildingTypes : F.projectTypes)} cols={2} />
                            <Choices round label={visit ? `${F.phase} *` : `${F.progress} *`} value={form.progress} onPick={set('progress')} error={errors.progress} options={opts(visit ? F.phases : F.progresses)} />
                            {!visit && (
                                <>
                                    <div className="g2" style={{ gap: 12 }}>
                                        {[['land_area', 'land_unknown', F.landArea], ['footprint_area', 'footprint_unknown', F.footprint]].map(([k, u, label]) => (
                                            <Field key={k} id={`cdp-${k}`} label={label} hint={F.areaHint} error={errors[k]}>
                                                <Input id={`cdp-${k}`} value={form[u] ? '' : form[k]} onChange={set(k)} error={errors[k]} hint={F.areaHint} inputMode="decimal" />
                                                <label className="f-check" style={{ fontSize: 14 }}>
                                                    <input type="checkbox" checked={form[u]} onChange={(e) => set(u)(e.target.checked)} />
                                                    <span>{F.unknown}</span>
                                                </label>
                                            </Field>
                                        ))}
                                    </div>
                                    <Field id="cdp-levels" label={F.levels} error={errors.levels}>
                                        <select id="cdp-levels" className="f-input" value={form.levels} onChange={(e) => set('levels')(e.target.value)}>
                                            <option value="">—</option>
                                            {opts(F.levelOptions).map(([v, t]) => (
                                                <option key={v} value={v}>{t}</option>
                                            ))}
                                        </select>
                                    </Field>
                                </>
                            )}
                            <Field id="cdp-desc" label={visit ? `${F.reason} *` : `${F.question} *`} hint={visit ? F.reasonHint : F.questionHint} error={errors.description}>
                                <Input id="cdp-desc" textarea value={form.description} onChange={set('description')} error={errors.description} hint={visit ? F.reasonHint : F.questionHint} required />
                            </Field>
                            {visit && (
                                <>
                                    <Field id="cdp-avail" label={F.availability} hint={F.availabilityHint} error={errors.availability}>
                                        <Input id="cdp-avail" value={form.availability} onChange={set('availability')} error={errors.availability} hint={F.availabilityHint} />
                                    </Field>
                                    <label className="f-check" htmlFor="cdp-danger">
                                        <input id="cdp-danger" type="checkbox" checked={form.danger} onChange={(e) => set('danger')(e.target.checked)} />
                                        <span>{F.danger}</span>
                                    </label>
                                    {form.danger && (
                                        <div role="alert" style={{ padding: '14px 16px', borderRadius: 14, border: '1.5px solid var(--v-err)', color: 'var(--v-err)', fontWeight: 600, lineHeight: 1.55 }}>
                                            {F.dangerAlert}
                                        </div>
                                    )}
                                </>
                            )}
                        </>
                    )}

                    {step === 2 && (
                        <>
                            <Field id="cdp-name" label={`${F.name} *`} error={errors.name}>
                                <Input id="cdp-name" value={form.name} onChange={set('name')} error={errors.name} autoComplete="name" required />
                            </Field>
                            <Field id="cdp-phone" label={`${F.phone} *`} hint={F.phoneHint} error={errors.phone}>
                                <Input id="cdp-phone" type="tel" inputMode="tel" value={form.phone} onChange={set('phone')} error={errors.phone} hint={F.phoneHint} autoComplete="tel" required />
                            </Field>
                            <Field id="cdp-email" label={F.email} error={errors.email}>
                                <Input id="cdp-email" type="email" inputMode="email" value={form.email} onChange={set('email')} error={errors.email} autoComplete="email" />
                            </Field>
                            {!visit && <Choices round label={F.mode} value={form.meeting_mode} onPick={set('meeting_mode')} options={opts(F.modes)} />}
                            {visit && (
                                <Field id="cdp-access" label={F.access} hint={F.accessHint} error={errors.access_constraints}>
                                    <Input id="cdp-access" value={form.access_constraints} onChange={set('access_constraints')} error={errors.access_constraints} hint={F.accessHint} />
                                </Field>
                            )}
                            <UploadZone
                                id="cdp-files"
                                files={files}
                                onChange={setFiles}
                                label={visit ? F.photoLabel : F.pdfLabel}
                                hint={visit ? F.filesHintPhoto(limits.photo_max_files, limits.photo_max_mb) : F.filesHintPdf(limits.pdf_max_files, limits.pdf_max_mb)}
                                exts={visit ? ['jpg', 'jpeg', 'png', 'webp'] : ['pdf']}
                                maxBytes={(visit ? limits.photo_max_mb : limits.pdf_max_mb) * 1024 * 1024}
                                maxFiles={visit ? limits.photo_max_files : limits.pdf_max_files}
                                messages={{ type: `${F.fileType} ${visit ? 'JPG, PNG, WebP' : 'PDF'}.`, size: F.fileSize, count: F.fileCount }}
                            />
                            {errors.attachments && <div className="f-err" role="alert">{errors.attachments}</div>}
                            <p className="f-muted" style={{ margin: 0, fontSize: 14, lineHeight: 1.55 }}>{F.dataNote}</p>
                            <div>
                                <label className="f-check" htmlFor="cdp-privacy">
                                    <input id="cdp-privacy" type="checkbox" checked={form.privacy} onChange={(e) => set('privacy')(e.target.checked)} aria-invalid={errors.privacy ? 'true' : 'false'} aria-describedby={errors.privacy ? 'cdp-privacy-err' : undefined} />
                                    <span>
                                        {F.privacy} *{' '}
                                        <A href="/confidentialite" target="_blank" style={{ color: 'var(--v-accent-text)', fontWeight: 700 }}>{F.privacyLink}</A>
                                    </span>
                                </label>
                                {errors.privacy && <div className="f-err" id="cdp-privacy-err" role="alert" style={{ marginTop: 6 }}>{errors.privacy}</div>}
                            </div>
                            <label className="f-check" htmlFor="cdp-marketing">
                                <input id="cdp-marketing" type="checkbox" checked={form.marketing} onChange={(e) => set('marketing')(e.target.checked)} />
                                <span>{F.marketing}</span>
                            </label>
                        </>
                    )}

                    <div style={{ display: 'flex', gap: 10, marginTop: 8 }}>
                        {step === 2 && (
                            <button type="button" className="f-btn f-btn-ghost f-btn-inline" onClick={() => { setStep(1); setBanner(''); focusTop(); }} disabled={sending}>
                                {F.back}
                            </button>
                        )}
                        <button type="submit" className="f-btn f-btn-primary" style={{ flex: '1 1 0', minWidth: 0 }} disabled={sending} aria-busy={sending}>
                            {sending ? F.sending : step === 1 ? F.next : F.send}
                        </button>
                    </div>
                    {progress != null && (
                        <div role="status" aria-live="polite" style={{ fontSize: 14 }}>
                            {F.uploading} : {progress} %
                            <div aria-hidden="true" style={{ height: 4, marginTop: 6, borderRadius: 4, background: 'var(--v-line-strong)' }}>
                                <div style={{ width: `${progress}%`, height: 4, borderRadius: 4, background: '#FE5000', transition: 'width .2s' }} />
                            </div>
                        </div>
                    )}
                    <p className="f-muted" style={{ margin: 0, fontSize: 13, textAlign: 'center' }}>{F.reassure}</p>
                    <input type="text" name="website" tabIndex={-1} autoComplete="off" aria-hidden="true" style={{ position: 'absolute', left: -9999, width: 1, height: 1 }} />
                </div>
            </form>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* Confirmation — only after the server stored the request (CDC §09)   */
/* ------------------------------------------------------------------ */

export function CdpConfirmation({ reference, message, waiting = false }) {
    const { C } = useCdp();
    const [copied, setCopied] = useState(false);
    const box = useRef(null);
    useEffect(() => box.current?.focus(), []);
    const copy = async () => {
        try {
            await navigator.clipboard.writeText(reference);
            setCopied(true);
        } catch (e) {
            setCopied(false);
        }
    };
    return (
        <div ref={box} tabIndex={-1} role="status" className="f-card f-panel f-pad" style={{ padding: 40, outline: 'none', maxWidth: 720, margin: '0 auto' }}>
            <div style={{ width: 64, height: 64, borderRadius: 999, background: 'rgba(1,178,124,.16)', color: '#01B27C', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <Icon name="check" size={30} stroke={2.4} />
            </div>
            <div className="f-eye" style={{ marginTop: 20, color: '#01B27C' }}>{C.confirm.eyebrow}</div>
            <h1 style={{ margin: '10px 0 0', fontSize: 'clamp(26px, 4vw, 36px)' }}>{C.confirm.title}</h1>
            <p style={{ margin: '18px 0 0', fontSize: 18 }}>
                {C.confirm.ref}{' '}: <strong className="f-accent" style={{ fontFamily: "'Clash Display', sans-serif", letterSpacing: '.04em' }}>{reference}</strong>
            </p>
            {waiting && <p style={{ margin: '14px 0 0', lineHeight: 1.65, fontWeight: 700 }}>{C.confirm.waiting}</p>}
            <p className="f-muted" style={{ margin: '14px 0 0', lineHeight: 1.65 }}>{message || C.confirm.text}</p>
            <p className="f-muted" style={{ margin: '10px 0 0', fontSize: 14 }}>{C.confirm.keep}</p>
            <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap', marginTop: 26 }}>
                <button type="button" className="f-btn f-btn-primary f-btn-inline" onClick={copy}>{copied ? C.confirm.copied : C.confirm.copy}</button>
                <A className="f-btn f-btn-ghost f-btn-inline" href="/">{C.confirm.back}</A>
            </div>
            <span className="sr-only" aria-live="polite">{copied ? C.confirm.copied : ''}</span>
        </div>
    );
}
