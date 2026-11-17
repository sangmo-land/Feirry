import { useEffect, useMemo, useRef, useState } from 'react';
import { router } from '@inertiajs/react';
import { A, Icon, useV7 } from './core';

const EXT = ['pdf', 'jpg', 'jpeg', 'png', 'dwg'];
const MAX_BYTES = 10 * 1024 * 1024;
const MAX_FILES = 5;
const digits = (v) => (v || '').replace(/\D/g, '').length;
const emailOk = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v || '');

/** Client checks mirror the server rules; the server stays the authority. */
function checkFiles(list, current, F) {
    const errors = [];
    const ok = [];
    for (const f of list) {
        const ext = (f.name.split('.').pop() || '').toLowerCase();
        if (!EXT.includes(ext)) errors.push(`« ${f.name} » ${F.errFileType}`);
        else if (f.size > MAX_BYTES) errors.push(`« ${f.name} » ${F.errFileSize}`);
        else ok.push(f);
    }
    const merged = [...current, ...ok];
    if (merged.length > MAX_FILES) errors.push(F.errFileCount);
    return { files: merged.slice(0, MAX_FILES), errors };
}

export function UploadZone({ id, files, onChange, label, hint, required = false }) {
    const { L } = useV7();
    const F = L.form;
    const [errors, setErrors] = useState([]);
    const [drag, setDrag] = useState(false);
    const add = (list) => {
        const r = checkFiles(Array.from(list || []), files, F);
        setErrors(r.errors);
        onChange(r.files);
    };
    return (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
            <label
                htmlFor={id}
                className={`f-drop${drag ? ' drag' : ''}`}
                onDragOver={(e) => {
                    e.preventDefault();
                    setDrag(true);
                }}
                onDragLeave={() => setDrag(false)}
                onDrop={(e) => {
                    e.preventDefault();
                    setDrag(false);
                    add(e.dataTransfer.files);
                }}
            >
                <Icon name="upload" size={22} color="#FE5000" />
                <span style={{ flex: 1 }}>
                    <span style={{ display: 'block', color: 'var(--v-text)' }}>{label}{required ? ' *' : ''}</span>
                    <span style={{ display: 'block', fontSize: 13, fontWeight: 500 }}>{hint}</span>
                </span>
                <input id={id} type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.dwg" onChange={(e) => { add(e.target.files); e.target.value = ''; }} aria-describedby={`${id}-err`} />
            </label>
            {files.length > 0 && (
                <ul className="f-list" style={{ gap: 6, fontSize: 14 }}>
                    {files.map((f, i) => (
                        <li key={`${f.name}-${i}`} style={{ alignItems: 'center', justifyContent: 'space-between', padding: '6px 6px 6px 12px', border: '1px solid var(--v-line)', borderRadius: 12 }}>
                            <span style={{ display: 'flex', gap: 8, alignItems: 'center', minWidth: 0 }}>
                                <Icon name="file" size={16} />
                                <span style={{ overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{f.name}</span>
                                <span className="f-muted" style={{ flex: 'none' }}>{Math.max(1, Math.round(f.size / 1024))} {L.kb}</span>
                            </span>
                            <button type="button" className="f-textbtn" style={{ minHeight: 36, padding: '0 8px', fontSize: 13 }} onClick={() => onChange(files.filter((_, j) => j !== i))}>
                                {F.remove}
                                <span className="sr-only"> {f.name}</span>
                            </button>
                        </li>
                    ))}
                </ul>
            )}
            <div id={`${id}-err`} role={errors.length ? 'alert' : undefined}>
                {errors.map((e) => (
                    <div key={e} className="f-err">{e}</div>
                ))}
            </div>
            <div className="f-muted" style={{ display: 'flex', gap: 8, alignItems: 'flex-start', fontSize: 13, lineHeight: 1.5 }}>
                <Icon name="lock" size={16} style={{ marginTop: 2 }} />
                {L.cdpSecure}
            </div>
        </div>
    );
}

function Field({ id, label, error, hint, children }) {
    return (
        <div className="f-field">
            <label htmlFor={id}>{label}</label>
            {hint && <span className="hint" id={`${id}-hint`}>{hint}</span>}
            {children}
            {error && (
                <span className="f-err" id={`${id}-err`} role="alert">
                    {error}
                </span>
            )}
        </div>
    );
}

function Input({ id, value, onChange, error, hint, type = 'text', autoComplete, inputMode, textarea = false, required = false }) {
    const props = {
        id,
        className: 'f-input',
        value: value ?? '',
        onChange: (e) => onChange(e.target.value),
        'aria-invalid': error ? 'true' : 'false',
        'aria-describedby': [error ? `${id}-err` : null, hint ? `${id}-hint` : null].filter(Boolean).join(' ') || undefined,
        'aria-required': required || undefined,
        autoComplete,
        inputMode,
        maxLength: textarea ? 3000 : 180,
    };
    return textarea ? <textarea rows={3} {...props} /> : <input type={type} {...props} />;
}

function Choices({ label, options, value, onPick, round = false, cols = 2, error }) {
    return (
        <fieldset style={{ border: 0, margin: 0, padding: 0 }}>
            <legend style={{ fontSize: 14, fontWeight: 700, marginBottom: 10, padding: 0 }}>{label}</legend>
            <div className={round ? 'chips' : `g${cols}`} style={{ gap: round ? 8 : 10 }}>
                {options.map(([v, title, sub]) => (
                    <button key={v} type="button" className={`f-choice${round ? ' round' : ''}`} aria-pressed={value === v} onClick={() => onPick(value === v && round ? '' : v)}>
                        <span style={{ display: 'block' }}>{title}</span>
                        {sub && <span className="f-muted" style={{ display: 'block', marginTop: 2, fontSize: 13, fontWeight: 500 }}>{sub}</span>}
                    </button>
                ))}
            </div>
            {error && <div className="f-err" role="alert" style={{ marginTop: 8 }}>{error}</div>}
        </fieldset>
    );
}

function Consent({ id, checked, onChange, error }) {
    const { L } = useV7();
    return (
        <div>
            <label className="f-check" htmlFor={id}>
                <input id={id} type="checkbox" checked={checked} onChange={(e) => onChange(e.target.checked)} aria-invalid={error ? 'true' : 'false'} aria-describedby={error ? `${id}-err` : undefined} />
                <span>
                    {L.form.privacy}{' '}
                    <A href="/confidentialite" target="_blank" style={{ color: 'var(--v-accent-text)', fontWeight: 700 }}>
                        {L.form.privacyLink}
                    </A>
                </span>
            </label>
            {error && <div className="f-err" id={`${id}-err`} role="alert" style={{ marginTop: 6 }}>{error}</div>}
        </div>
    );
}

function Banner({ text }) {
    if (!text) return null;
    return (
        <div role="alert" style={{ padding: '12px 14px', borderRadius: 14, border: '1px solid var(--v-err)', color: 'var(--v-err)', fontWeight: 600, fontSize: 14, lineHeight: 1.5 }}>
            {text}
        </div>
    );
}

function failureText(F, kind, status) {
    if (kind === 'network') return F.errNetwork;
    if (status === 429) return F.errThrottle;
    return F.errServer;
}

/* ------------------------------------------------------------------ */
/* « Mon projet » — five steps (CDC §08)                               */
/* ------------------------------------------------------------------ */

const STORE = 'feirry-projet';
const EMPTY = { need: '', offer: '', location: '', programme: '', phase: '', land_status: '', size: '', budget_range: '', name: '', phone: '', email: '', contact_preference: 'telephone', message: '', privacy: false };

const STEP_OF = { need: 1, offer: 1, location: 2, programme: 2, phase: 2, land_status: 2, size: 2, budget_range: 2, name: 3, phone: 3, email: 3, contact_preference: 3, message: 3, attachments: 4, privacy: 5 };

export function ProjectWizard({ offers = [], preset = {} }) {
    const { L, path } = useV7();
    const F = L.form;
    const top = useRef(null);
    const [step, setStep] = useState(1);
    const [form, setForm] = useState(EMPTY);
    const [files, setFiles] = useState([]);
    const [errors, setErrors] = useState({});
    const [banner, setBanner] = useState('');
    const [sending, setSending] = useState(false);
    const [reference, setReference] = useState('');

    // Restore the session draft, then apply an offer/expertise preset (need only).
    useEffect(() => {
        let draft = {};
        try {
            draft = JSON.parse(sessionStorage.getItem(STORE) || '{}');
        } catch (e) {
            draft = {};
        }
        const next = { ...EMPTY, ...draft, privacy: false };
        if (preset?.need) next.need = preset.need;
        if (preset?.offer) next.offer = preset.offer;
        setForm(next);
        if (preset?.need) setStep(2);
    }, [preset?.need, preset?.offer]);

    useEffect(() => {
        const { privacy, ...rest } = form;
        try {
            sessionStorage.setItem(STORE, JSON.stringify(rest));
        } catch (e) {
            /* storage unavailable: the form still works */
        }
    }, [form]);

    const set = (k) => (v) => {
        setForm((f) => ({ ...f, [k]: v }));
        setErrors((e) => ({ ...e, [k]: undefined }));
    };

    const focusTop = () => window.setTimeout(() => top.current?.focus({ preventScroll: false }), 20);

    const validate = (s) => {
        const e = {};
        if (s === 1 && !form.need) e.need = F.errType;
        if (s === 2 && form.location.trim().length < 2) e.location = F.errLocation;
        if (s === 3) {
            if (form.name.trim().length < 2) e.name = F.errName;
            if (!form.phone.trim() && !form.email.trim()) e.phone = F.errContact;
            if (form.phone.trim() && (digits(form.phone) < 9 || digits(form.phone) > 15)) e.phone = F.errPhone;
            if (form.email.trim() && !emailOk(form.email.trim())) e.email = F.errEmail;
        }
        if (s === 5 && !form.privacy) e.privacy = F.errPrivacy;
        setErrors(e);
        return Object.keys(e).length === 0;
    };

    const go = (n) => {
        setStep(n);
        setBanner('');
        focusTop();
    };

    const next = () => {
        if (!validate(step)) return;
        if (step < 5) return go(step + 1);
        submit();
    };

    const submit = () => {
        setSending(true);
        setBanner('');
        const data = { ...form, privacy: form.privacy ? 1 : 0, website: '' };
        files.forEach((f, i) => (data[`attachments[${i}]`] = f));
        router.post(path('/mon-projet'), data, {
            forceFormData: true,
            preserveScroll: true,
            preserveState: true,
            onSuccess: (page) => {
                const ref = page.props.flash?.reference;
                if (ref) {
                    setReference(ref);
                    setStep(6);
                    setFiles([]);
                    try {
                        sessionStorage.removeItem(STORE);
                    } catch (e) {
                        /* ignore */
                    }
                    focusTop();
                } else {
                    setBanner(F.errServer);
                }
            },
            onError: (errs) => {
                const mapped = {};
                Object.entries(errs).forEach(([k, v]) => (mapped[k.startsWith('attachments') ? 'attachments' : k] = v));
                setErrors(mapped);
                const first = Math.min(...Object.keys(mapped).map((k) => STEP_OF[k] ?? 5));
                setBanner(F.errCheck);
                setStep(Number.isFinite(first) ? first : 5);
                focusTop();
            },
            onNetworkError: () => {
                setBanner(failureText(F, 'network'));
                return false;
            },
            onHttpException: (response) => {
                setBanner(failureText(F, 'http', response?.status));
                return false;
            },
            onFinish: () => setSending(false),
        });
    };

    const restart = () => {
        setForm(EMPTY);
        setFiles([]);
        setErrors({});
        setReference('');
        go(1);
    };

    const offer = offers.find((o) => o.slug === form.offer);
    const needLabel = (v) => (F.needs[v] ? F.needs[v][0] : F.notGiven);
    const review = [
        [F.stepNames[0], [needLabel(form.need), offer ? `${F.fromOffer} ${offer.title}` : null].filter(Boolean).join(' · '), 1],
        [F.stepNames[1], [form.location, form.programme, F.phases[form.phase], F.lands[form.land_status], form.size, form.budget_range].filter(Boolean).join(' · ') || F.notGiven, 2],
        [F.stepNames[2], [form.name, form.phone, form.email, F.prefs[form.contact_preference]].filter(Boolean).join(' · '), 3],
        [F.stepNames[3], files.length ? files.map((f) => f.name).join(', ') : F.noFiles, 4],
    ];

    return (
        <div className="f-card f-panel f-pad" style={{ padding: 32 }}>
            <form noValidate onSubmit={(e) => { e.preventDefault(); next(); }} aria-labelledby="wiz-title">
                <div ref={top} tabIndex={-1} style={{ outline: 'none' }}>
                    {step < 6 && (
                        <>
                            <ol aria-label={F.progressAria} style={{ display: 'flex', gap: 6, margin: '0 0 10px', padding: 0, listStyle: 'none' }}>
                                {F.stepNames.map((n, i) => (
                                    <li key={n} aria-current={i + 1 === step ? 'step' : undefined} style={{ flex: 1, height: 4, borderRadius: 4, background: i + 1 <= step ? '#FE5000' : 'var(--v-line-strong)' }}>
                                        <span className="sr-only">{n}</span>
                                    </li>
                                ))}
                            </ol>
                            <div className="f-muted" style={{ display: 'flex', justifyContent: 'space-between', gap: 12, fontSize: 13, fontWeight: 700, marginBottom: 18 }}>
                                <span>{F.stepOf} {step} {F.of} 5</span>
                                <span className="f-accent">{F.stepNames[step - 1]}</span>
                            </div>
                        </>
                    )}
                </div>

                <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                    <Banner text={banner} />

                    {step === 1 && (
                        <>
                            <h3 id="wiz-title" style={{ margin: 0, fontSize: 22 }}>{F.q1}</h3>
                            {offer && <p className="f-muted" style={{ margin: 0, fontSize: 14 }}>{F.fromOffer} <strong style={{ color: 'var(--v-text)' }}>{offer.title}</strong></p>}
                            <Choices label={<span className="sr-only">{F.q1}</span>} value={form.need} onPick={set('need')} error={errors.need} options={Object.entries(F.needs).map(([v, [t, s]]) => [v, t, s])} />
                        </>
                    )}

                    {step === 2 && (
                        <>
                            <h3 id="wiz-title" style={{ margin: 0, fontSize: 22 }}>{F.q2}</h3>
                            <Field id="w-location" label={F.location} error={errors.location}>
                                <Input id="w-location" value={form.location} onChange={set('location')} error={errors.location} autoComplete="address-level2" required />
                            </Field>
                            <Field id="w-programme" label={F.programme} hint={F.programmeHint} error={errors.programme}>
                                <Input id="w-programme" value={form.programme} onChange={set('programme')} error={errors.programme} hint={F.programmeHint} />
                            </Field>
                            <Choices round label={F.phase} value={form.phase} onPick={set('phase')} options={Object.entries(F.phases).map(([v, t]) => [v, t])} />
                            <Choices round label={F.land} value={form.land_status} onPick={set('land_status')} options={Object.entries(F.lands).map(([v, t]) => [v, t])} />
                            <div className="g2" style={{ gap: 12 }}>
                                <Field id="w-size" label={F.size} error={errors.size}>
                                    <Input id="w-size" value={form.size} onChange={set('size')} error={errors.size} />
                                </Field>
                                <Field id="w-budget" label={F.budget} error={errors.budget_range}>
                                    <Input id="w-budget" value={form.budget_range} onChange={set('budget_range')} error={errors.budget_range} />
                                </Field>
                            </div>
                        </>
                    )}

                    {step === 3 && (
                        <>
                            <h3 id="wiz-title" style={{ margin: 0, fontSize: 22 }}>{F.q3}</h3>
                            <Field id="w-name" label={F.name} error={errors.name}>
                                <Input id="w-name" value={form.name} onChange={set('name')} error={errors.name} autoComplete="name" required />
                            </Field>
                            <p className="f-muted" style={{ margin: 0, fontSize: 13 }}>{F.contactHint}</p>
                            <div className="g2" style={{ gap: 12 }}>
                                <Field id="w-phone" label={F.phone} error={errors.phone}>
                                    <Input id="w-phone" type="tel" inputMode="tel" value={form.phone} onChange={set('phone')} error={errors.phone} autoComplete="tel" />
                                </Field>
                                <Field id="w-email" label={F.email} error={errors.email}>
                                    <Input id="w-email" type="email" inputMode="email" value={form.email} onChange={set('email')} error={errors.email} autoComplete="email" />
                                </Field>
                            </div>
                            <Choices round label={F.pref} value={form.contact_preference} onPick={(v) => set('contact_preference')(v || 'telephone')} options={Object.entries(F.prefs).map(([v, t]) => [v, t])} error={errors.contact_preference} />
                            <Field id="w-msg" label={F.msg} error={errors.message}>
                                <Input id="w-msg" textarea value={form.message} onChange={set('message')} error={errors.message} />
                            </Field>
                        </>
                    )}

                    {step === 4 && (
                        <>
                            <h3 id="wiz-title" style={{ margin: 0, fontSize: 22 }}>{F.q4}</h3>
                            <p className="f-muted" style={{ margin: 0, lineHeight: 1.55 }}>{F.q4help}</p>
                            <UploadZone id="w-files" files={files} onChange={setFiles} label={F.files} hint={F.filesHint} />
                            {errors.attachments && <div className="f-err" role="alert">{errors.attachments}</div>}
                        </>
                    )}

                    {step === 5 && (
                        <>
                            <h3 id="wiz-title" style={{ margin: 0, fontSize: 22 }}>{F.q5}</h3>
                            <dl style={{ margin: 0 }}>
                                {review.map(([k, v, n]) => (
                                    <div key={k} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 12, padding: '12px 0', borderTop: '1px solid var(--v-line)' }}>
                                        <div style={{ minWidth: 0 }}>
                                            <dt className="f-muted" style={{ fontSize: 13 }}>{k}</dt>
                                            <dd style={{ margin: '4px 0 0', fontWeight: 700, lineHeight: 1.45, overflowWrap: 'anywhere' }}>{v}</dd>
                                        </div>
                                        <button type="button" className="f-textbtn" onClick={() => go(n)} style={{ flex: 'none', fontSize: 14, padding: '0 4px' }}>
                                            {F.edit}
                                            <span className="sr-only"> : {k}</span>
                                        </button>
                                    </div>
                                ))}
                            </dl>
                            <Consent id="w-privacy" checked={form.privacy} onChange={set('privacy')} error={errors.privacy} />
                        </>
                    )}

                    {step === 6 && (
                        <div role="status" style={{ textAlign: 'center', padding: '20px 4px' }}>
                            <div style={{ width: 64, height: 64, margin: '0 auto', borderRadius: 999, background: 'rgba(1,178,124,.16)', color: '#01B27C', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                <Icon name="check" size={30} stroke={2.4} />
                            </div>
                            <h3 id="wiz-title" style={{ margin: '18px 0 8px', fontSize: 26 }}>
                                {F.doneTitle}
                                {form.name.trim() ? `, ${form.name.trim().split(' ')[0]}` : ''}.
                            </h3>
                            <p className="f-muted" style={{ margin: '0 0 10px', lineHeight: 1.6 }}>{F.doneText}</p>
                            <p style={{ margin: '0 0 6px', fontWeight: 700 }}>
                                {F.doneRef} <span className="f-accent">{reference}</span>
                            </p>
                            <p className="f-muted" style={{ margin: '0 0 22px', fontSize: 13 }}>{F.doneNext}</p>
                            <button type="button" onClick={restart} className="f-btn f-btn-sm f-btn-ghost f-btn-inline">{F.restart}</button>
                        </div>
                    )}

                    {step < 6 && (
                        <div style={{ display: 'flex', gap: 10, marginTop: 8 }}>
                            {step > 1 && (
                                <button type="button" onClick={() => go(step - 1)} className="f-btn f-btn-ghost f-btn-inline" disabled={sending}>
                                    {F.back}
                                </button>
                            )}
                            <button type="submit" className="f-btn f-btn-primary" style={{ flex: '1 1 0', minWidth: 0 }} disabled={sending} aria-busy={sending}>
                                {sending ? F.sending : step === 5 ? F.send : step === 4 && !files.length ? F.skip : F.next}
                            </button>
                        </div>
                    )}
                    <input type="text" name="website" tabIndex={-1} autoComplete="off" aria-hidden="true" style={{ position: 'absolute', left: -9999, width: 1, height: 1 }} />
                </div>
            </form>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* Coup de Pouce — consultation or site visit (CDC §08)                */
/* ------------------------------------------------------------------ */

const CDP_EMPTY = { name: '', phone: '', email: '', city: '', project_type: '', description: '', meeting_mode: '', site_location: '', objective: '', access_constraints: '', availability: '', privacy: false };

export function CdpForm({ campaign, compact = false }) {
    const { L, path } = useV7();
    const F = L.form;
    const paths = ['consultation', 'visit'].filter((p) => campaign[p] !== false);
    const [mode, setMode] = useState(paths[0] ?? 'consultation');
    const [form, setForm] = useState(CDP_EMPTY);
    const [files, setFiles] = useState([]);
    const [errors, setErrors] = useState({});
    const [banner, setBanner] = useState('');
    const [sending, setSending] = useState(false);
    const [reference, setReference] = useState('');
    const box = useRef(null);

    if (!campaign.open) {
        return (
            <div role="status" style={{ padding: 24, borderRadius: 20, border: '1px solid var(--v-line-strong)', background: 'var(--v-surface)' }}>
                <div style={{ fontFamily: "'Clash Display', sans-serif", fontSize: 22, fontWeight: 700 }}>{L.cdpClosedTitle}</div>
                <p className="f-muted" style={{ margin: '8px 0 16px', lineHeight: 1.6 }}>{L.cdpClosedText}</p>
                <div className="g2" style={{ gap: 8, marginBottom: 16 }}>
                    {paths.map((p) => (
                        <div key={p} style={{ padding: '12px 14px', borderRadius: 16, border: '1.5px solid var(--v-line-strong)' }}>
                            <span style={{ display: 'block', fontWeight: 800, fontSize: 15 }}>{L.cdpPaths[p][0]}</span>
                            <span className="f-muted" style={{ display: 'block', marginTop: 2, fontSize: 13 }}>{L.cdpPaths[p][1]}</span>
                        </div>
                    ))}
                </div>
                <A className="f-btn f-btn-primary" href="/mon-projet">{L.cdpClosedCta}</A>
            </div>
        );
    }

    const set = (k) => (v) => {
        setForm((f) => ({ ...f, [k]: v }));
        setErrors((e) => ({ ...e, [k]: undefined }));
    };
    const visit = mode === 'visit';

    const validate = () => {
        const e = {};
        if (form.name.trim().length < 2) e.name = F.errName;
        if (!form.phone.trim() && !form.email.trim()) e.phone = F.errContact;
        if (form.phone.trim() && digits(form.phone) < 9) e.phone = F.errPhone;
        if (form.email.trim() && !emailOk(form.email)) e.email = F.errEmail;
        if (!visit) {
            if (!form.city.trim()) e.city = F.errRequired;
            if (!form.description.trim()) e.description = F.errRequired;
            if (!form.meeting_mode) e.meeting_mode = F.errRequired;
            if (campaign.documents_required && !files.length) e.attachments = F.errRequired;
        } else {
            if (!form.site_location.trim()) e.site_location = F.errRequired;
            if (!form.objective.trim()) e.objective = F.errRequired;
            if (!form.availability.trim()) e.availability = F.errRequired;
        }
        if (!form.privacy) e.privacy = F.errPrivacy;
        setErrors(e);
        return Object.keys(e).length === 0;
    };

    const submit = (ev) => {
        ev.preventDefault();
        setBanner('');
        if (!validate()) {
            setBanner(F.errCheck);
            return;
        }
        setSending(true);
        const data = { ...form, path: mode, privacy: 1, website: '' };
        files.forEach((f, i) => (data[`attachments[${i}]`] = f));
        router.post(path('/coup-de-pouce'), data, {
            forceFormData: true,
            preserveScroll: true,
            preserveState: true,
            onSuccess: (page) => {
                if (page.props.flash?.reference) {
                    setReference(page.props.flash.reference);
                    window.setTimeout(() => box.current?.focus(), 20);
                } else if (page.props.flash?.error === 'campaign_closed') {
                    setBanner(F.campaignClosed);
                } else {
                    setBanner(F.errServer);
                }
            },
            onError: (errs) => {
                const mapped = {};
                Object.entries(errs).forEach(([k, v]) => (mapped[k.startsWith('attachments') ? 'attachments' : k] = v));
                setErrors(mapped);
                setBanner(F.errCheck);
            },
            onNetworkError: () => {
                setBanner(F.errNetwork);
                return false;
            },
            onHttpException: (r) => {
                setBanner(failureText(F, 'http', r?.status));
                return false;
            },
            onFinish: () => setSending(false),
        });
    };

    if (reference) {
        return (
            <div ref={box} tabIndex={-1} role="status" style={{ padding: 24, borderRadius: 20, background: 'rgba(1,178,124,.14)', border: '1px solid rgba(1,178,124,.5)', outline: 'none' }}>
                <div style={{ fontFamily: "'Clash Display', sans-serif", fontSize: 22, fontWeight: 700 }}>{L.cdpThanks}</div>
                <p className="f-muted" style={{ margin: '8px 0 8px', lineHeight: 1.6 }}>{L.cdpThanksText[mode]}</p>
                <p style={{ margin: '0 0 16px', fontWeight: 700 }}>{F.doneRef} <span className="f-accent">{reference}</span></p>
                <button type="button" onClick={() => { setReference(''); setForm(CDP_EMPTY); setFiles([]); }} className="f-btn f-btn-sm f-btn-ghost f-btn-inline">{F.restart}</button>
            </div>
        );
    }

    return (
        <form onSubmit={submit} noValidate style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
            <div role="radiogroup" aria-label={L.cdpPathAria} className="g2" style={{ gap: 8, marginBottom: 4 }}>
                {paths.map((p) => (
                    <button key={p} type="button" role="radio" aria-checked={mode === p} className="f-choice" onClick={() => { setMode(p); setErrors({}); }} style={{ minHeight: 64 }}>
                        <span style={{ display: 'block', fontWeight: 800 }}>{L.cdpPaths[p][0]}</span>
                        <span className="f-muted" style={{ display: 'block', marginTop: 2, fontSize: 13, fontWeight: 500 }}>{L.cdpPaths[p][1]}</span>
                    </button>
                ))}
            </div>
            <Banner text={banner} />
            <div className="g2" style={{ gap: 12 }}>
                <Field id="c-name" label={F.name} error={errors.name}>
                    <Input id="c-name" value={form.name} onChange={set('name')} error={errors.name} autoComplete="name" required />
                </Field>
                <Field id="c-phone" label={F.phone} error={errors.phone}>
                    <Input id="c-phone" type="tel" inputMode="tel" value={form.phone} onChange={set('phone')} error={errors.phone} autoComplete="tel" />
                </Field>
            </div>
            {!compact && (
                <Field id="c-email" label={F.email} error={errors.email}>
                    <Input id="c-email" type="email" value={form.email} onChange={set('email')} error={errors.email} autoComplete="email" />
                </Field>
            )}
            {!visit ? (
                <>
                    <div className="g2" style={{ gap: 12 }}>
                        <Field id="c-city" label={`${F.location.replace(' *', '').split(' ')[0]} *`} error={errors.city}>
                            <Input id="c-city" value={form.city} onChange={set('city')} error={errors.city} autoComplete="address-level2" required />
                        </Field>
                        <Field id="c-type" label={L.cdpType} error={errors.project_type}>
                            <select id="c-type" className="f-input" value={form.project_type} onChange={(e) => set('project_type')(e.target.value)}>
                                <option value="">—</option>
                                {Object.entries(F.needs).map(([v, [t]]) => (
                                    <option key={v} value={v}>{t}</option>
                                ))}
                            </select>
                        </Field>
                    </div>
                    <Field id="c-desc" label={L.cdpDesc} error={errors.description}>
                        <Input id="c-desc" textarea value={form.description} onChange={set('description')} error={errors.description} required />
                    </Field>
                    <Choices round label={L.cdpMeet} value={form.meeting_mode} onPick={set('meeting_mode')} error={errors.meeting_mode} options={Object.entries(L.meet).map(([v, t]) => [v, t])} />
                    <UploadZone id="c-files" files={files} onChange={setFiles} label={L.cdpDocs} hint={`${campaign.documents_required ? L.cdpDocsReq : L.cdpDocsOpt} · ${F.filesHint}`} required={campaign.documents_required} />
                    {errors.attachments && <div className="f-err" role="alert">{errors.attachments}</div>}
                </>
            ) : (
                <>
                    <Field id="c-place" label={L.cdpPlace} hint={L.cdpPlaceHint} error={errors.site_location}>
                        <Input id="c-place" value={form.site_location} onChange={set('site_location')} error={errors.site_location} hint={L.cdpPlaceHint} required />
                    </Field>
                    <Field id="c-obj" label={L.cdpObjective} error={errors.objective}>
                        <Input id="c-obj" textarea value={form.objective} onChange={set('objective')} error={errors.objective} required />
                    </Field>
                    <div className="g2" style={{ gap: 12 }}>
                        <Field id="c-access" label={L.cdpAccess} error={errors.access_constraints}>
                            <Input id="c-access" value={form.access_constraints} onChange={set('access_constraints')} error={errors.access_constraints} />
                        </Field>
                        <Field id="c-avail" label={L.cdpAvail} hint={L.cdpAvailHint} error={errors.availability}>
                            <Input id="c-avail" value={form.availability} onChange={set('availability')} error={errors.availability} hint={L.cdpAvailHint} required />
                        </Field>
                    </div>
                </>
            )}
            <Consent id="c-privacy" checked={form.privacy} onChange={set('privacy')} error={errors.privacy} />
            <input type="text" name="website" tabIndex={-1} autoComplete="off" aria-hidden="true" style={{ position: 'absolute', left: -9999, width: 1, height: 1 }} />
            <button type="submit" className="f-btn f-btn-primary" disabled={sending} aria-busy={sending}>
                {sending ? F.sending : L.cdpSend[mode]}
            </button>
        </form>
    );
}
