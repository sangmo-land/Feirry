import { useCallback, useMemo, useState } from 'react';
import { A, CheckList, CloseButton, Dialog, Icon, Img, Media, RoleBadges, SectionHead, formatDate, rolesText, stageText, useV7, waLink } from './core';
import { ProjectWizard } from './forms';
import { GalleryStrip, Lightbox, ZoomCover, projectImages } from './gallery';

const img = (name) => `/storage/v7/${name}-1280.webp`;

/* 01 — HERO */
export function Hero() {
    const { L } = useV7();
    return (
        <section id="accueil" className="f-hero" style={{ position: 'relative', minHeight: 760, display: 'flex', alignItems: 'flex-end', overflow: 'hidden', background: '#0B090B' }}>
            <Img src={img('japoma5')} alt={L.heroAlt} pos="center 30%" sizes="100vw" priority className="" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} />
            <div aria-hidden="true" className="hide-mob" style={{ position: 'absolute', inset: 0, background: 'linear-gradient(90deg, rgba(11,9,11,.92) 0%, rgba(11,9,11,.78) 38%, rgba(11,9,11,.3) 66%, rgba(11,9,11,.05) 100%)' }} />
            <div aria-hidden="true" className="hide-mob" style={{ position: 'absolute', inset: 0, background: 'linear-gradient(0deg, rgba(11,9,11,.9) 0%, rgba(11,9,11,.25) 38%, rgba(11,9,11,0) 60%)' }} />
            <div aria-hidden="true" className="only-mob" style={{ position: 'absolute', inset: 0, background: 'linear-gradient(0deg, rgba(11,9,11,.96) 0%, rgba(11,9,11,.88) 48%, rgba(11,9,11,.35) 78%, rgba(11,9,11,.15) 100%)' }} />
            <div className="f-wrap f-herobox" style={{ position: 'relative', width: '100%', paddingTop: 120, paddingBottom: 64, color: '#F7F3EE' }}>
                <div className="chips f-herochips" style={{ marginBottom: 24 }}>
                    <span className="hide-mob" style={{ flex: 'none', padding: '8px 14px', borderRadius: 999, background: 'rgba(255,255,255,.1)', border: '1px solid rgba(255,255,255,.18)', fontSize: 12, fontWeight: 700, letterSpacing: '.08em' }}>{L.heroBadge}</span>
                    <span style={{ flex: 'none', padding: '8px 14px', borderRadius: 999, background: '#FE5000', color: '#0B090B', fontSize: 12, fontWeight: 700, letterSpacing: '.08em' }}>DOUALA · YAOUNDÉ</span>
                </div>
                <h1 className="f-h1">
                    {L.h1a}
                    <br />
                    {L.h1b}
                    <br />
                    <span style={{ color: '#FE5000' }}>{L.h1c}</span>
                </h1>
                <p className="f-lead f-herolead" style={{ marginTop: 24, maxWidth: 580, color: 'rgba(247,243,238,.88)' }}>{L.heroLead}</p>
                <div className="f-herobtns" style={{ display: 'flex', gap: 12, flexWrap: 'wrap', marginTop: 32 }}>
                    <a className="f-btn f-btn-primary" href="#contact">
                        {L.ctaPrimary}
                        <Icon name="arrow" size={18} stroke={2.2} />
                    </a>
                    <A className="f-btn f-btn-light" href="/expertises">
                        {L.ctaSecondary}
                    </A>
                </div>
                <div className="hide-mob" style={{ marginTop: 40, fontSize: 12, fontWeight: 700, letterSpacing: '.22em', color: 'rgba(247,243,238,.72)' }}>SHAPING A BRIGHTER TOMORROW</div>
            </div>
        </section>
    );
}

export function Facts() {
    const { L } = useV7();
    return (
        <section aria-label={L.factsAria} style={{ borderBottom: '1px solid var(--v-line)' }}>
            <ul className="f-wrap g4" style={{ gap: 0, listStyle: 'none', margin: '0 auto' }}>
                {L.facts.map(([icon, title, text], i) => (
                    <li key={title} className="f-stat" style={{ padding: '28px 24px 28px 0', borderRight: i < 3 ? '1px solid var(--v-line)' : 0, display: 'flex', gap: 14, alignItems: 'center' }}>
                        <div className="f-icon-tile">
                            <Icon name={icon} size={22} />
                        </div>
                        <div>
                            <div style={{ fontFamily: "'Clash Display', sans-serif", fontSize: 20, fontWeight: 700 }}>{title}</div>
                            <div className="f-muted" style={{ marginTop: 2, fontSize: 14 }}>{text}</div>
                        </div>
                    </li>
                ))}
            </ul>
        </section>
    );
}

/* 02 — DIFFÉRENCE */
export function Difference({ headingLevel = 'h2' }) {
    const { L } = useV7();
    const H = headingLevel;
    return (
        <section id="feirry" className="f-sec f-wrap" aria-labelledby="s-feirry">
            <div className="split">
                <div>
                    <div className="f-eye">{L.s2eye}</div>
                    <H className="f-h2" id="s-feirry">{L.s2title}</H>
                    <p className="f-lead" style={{ marginTop: 20 }}>{L.s2p1}</p>
                    <p className="f-lead" style={{ marginTop: 14 }}>{L.s2p2}</p>
                    <ul className="f-list" style={{ marginTop: 24, gap: 14 }}>
                        {L.benefits.map(([t, d]) => (
                            <li key={t}>
                                <Icon name="check" size={18} stroke={2.4} color="#FE5000" style={{ marginTop: 3 }} />
                                <span>
                                    <strong>{t}</strong> <span className="f-muted">— {d}</span>
                                </span>
                            </li>
                        ))}
                    </ul>
                </div>
                <div className="f-card" style={{ position: 'relative', height: 460, overflow: 'hidden', background: '#2a2420' }}>
                    <Img src={img('bang1')} alt={L.s2alt} pos="center 40%" />
                    <div aria-hidden="true" style={{ position: 'absolute', inset: 0, background: 'linear-gradient(0deg, rgba(11,9,11,.85) 0%, rgba(11,9,11,0) 55%)' }} />
                    <div style={{ position: 'absolute', left: 28, right: 28, bottom: 24, color: '#F7F3EE', fontFamily: "'Clash Display', sans-serif", fontSize: 22, fontWeight: 600, lineHeight: 1.3 }}>{L.s2quote}</div>
                </div>
            </div>
        </section>
    );
}

/* 03 — EXPERTISES */
export function ExpertiseCard({ e, index, onOpen, priority = false }) {
    const { L } = useV7();
    return (
        <article className="f-card f-panel f-hover f-tile" style={{ overflow: 'hidden', display: 'flex', flexDirection: 'column' }}>
            <div className="f-media" style={{ height: 200 }}>
                <Img src={e.cover_image} alt={`${L.provisional} : ${e.title}`} pos={e.cover_position} sizes="(max-width: 767px) 100vw, 33vw" priority={priority} />
                <span style={{ position: 'absolute', top: 16, left: 16, width: 44, height: 44, borderRadius: 12, background: '#FE5000', color: '#0B090B', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <Icon name={e.icon} size={22} />
                </span>
                <span className="f-pill" style={{ top: 16, right: 16, fontSize: 11 }}>{L.provisional}</span>
            </div>
            <div style={{ padding: 24, display: 'flex', flexDirection: 'column', flex: 1 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: 12 }}>
                    <span className="f-eye" style={{ fontSize: 12 }}>{e.family}</span>
                    <span className="f-muted" style={{ fontFamily: "'Clash Display', sans-serif", fontWeight: 600, fontSize: 14 }}>{String(index + 1).padStart(2, '0')}</span>
                </div>
                <h3 style={{ margin: '10px 0 0', fontSize: 22, lineHeight: 1.2, minHeight: '2.4em' }}>{e.title}</h3>
                <p className="f-muted" style={{ margin: '10px 0 20px', fontSize: 15, lineHeight: 1.6 }}>{e.excerpt}</p>
                {onOpen ? (
                    <button type="button" className="f-textbtn" onClick={onOpen} style={{ marginTop: 'auto', alignSelf: 'flex-start' }} aria-haspopup="dialog">
                        {L.expMore}
                        <Icon name="arrow" size={16} stroke={2.2} />
                    </button>
                ) : (
                    <A className="f-textbtn" href={`/expertises/${e.slug}`} style={{ marginTop: 'auto', alignSelf: 'flex-start' }}>
                        {L.expMore}
                        <span className="sr-only"> : {e.title}</span>
                        <Icon name="arrow" size={16} stroke={2.2} />
                    </A>
                )}
            </div>
        </article>
    );
}

export function ExpertiseModal({ e, onClose }) {
    const { L } = useV7();
    return (
        <Dialog open={!!e} onClose={onClose} label={e?.title} width={720}>
            {e && (
                <>
                    <div style={{ position: 'relative', height: 220 }} className="f-media">
                        <Img src={e.cover_image} alt={`${L.provisional} : ${e.title}`} pos={e.cover_position} />
                        <CloseButton overlay onClick={onClose} label={L.closeFiche} />
                        <span className="f-pill" style={{ left: 16, bottom: 16 }}>{L.provisional}</span>
                    </div>
                    <div className="f-pad" style={{ padding: 32 }}>
                        <div className="f-eye">{e.family}</div>
                        <h3 style={{ margin: '10px 0 0', fontSize: 30 }}>{e.title}</h3>
                        <p className="f-lead" style={{ marginTop: 16 }}>{e.body}</p>
                        <div className="f-eye muted" style={{ marginTop: 24 }}>{L.expIncl}</div>
                        <div style={{ marginTop: 12 }}>
                            <CheckList items={e.features ?? []} />
                        </div>
                        <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap', marginTop: 28 }}>
                            <A className="f-btn f-btn-primary" href={`/mon-projet?besoin=${e.need}`}>{L.expAsk}</A>
                            <A className="f-btn f-btn-ghost" href={`/expertises/${e.slug}`}>{L.expMore}</A>
                        </div>
                    </div>
                </>
            )}
        </Dialog>
    );
}

export function Expertises({ expertises, page = false }) {
    const { L } = useV7();
    const [open, setOpen] = useState(null);
    return (
        <section id="expertises" className="f-sec f-wrap" aria-labelledby="s-exp">
            {page ? <h2 className="sr-only" id="s-exp">{L.s3eye}</h2> : <SectionHead id="s-exp" eye={L.s3eye} title={L.s3title} lead={L.s3lead} />}
            <div className="g3">
                {expertises.map((e, i) => (
                    <ExpertiseCard key={e.slug} e={e} index={i} priority={page && i === 0} onOpen={page ? undefined : () => setOpen(e)} />
                ))}
            </div>
            <ExpertiseModal e={open} onClose={() => setOpen(null)} />
        </section>
    );
}

/* 04 — PROJETS */
const FILTERS = ['all', 'architecture', 'construction', 'design', 'suivi'];

export function ProjectFiche({ p, onClose, onZoom }) {
    const { L } = useV7();
    return (
        <Dialog open={!!p} onClose={onClose} label={p?.title}>
            {p && (
                <>
                    <ZoomCover src={p.cover_image} title={p.title} pos={p.cover_position} count={projectImages(p).length} onOpen={() => onZoom(0)} height={260}>
                        <CloseButton overlay onClick={onClose} label={L.closeFiche} />
                        {p.provisional && <span className="f-pill" style={{ left: 16, bottom: 16 }}>{L.provisional}</span>}
                    </ZoomCover>
                    <GalleryStrip images={p.gallery} from={p.cover_image ? 1 : 0} onOpen={onZoom} />
                    <div className="f-pad" style={{ padding: '30px 32px 32px' }}>
                        <div className="f-eye">{L.ficheEye}</div>
                        <h3 style={{ margin: '8px 0 4px', fontSize: 30 }}>{p.title}</h3>
                        <div className="f-muted">{p.location}</div>
                        <div className="chips" style={{ marginTop: 14 }}>
                            <RoleBadges roles={p.roles} />
                        </div>
                        <ProjectFacts p={p} />
                        <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap', marginTop: 24 }}>
                            <A className="f-btn f-btn-primary" href={`/projets/${p.slug}`}>{L.pageSheet}</A>
                        </div>
                        <p className="f-muted" style={{ margin: '16px 0 0', fontSize: 14 }}>{L.ficheNote}</p>
                    </div>
                </>
            )}
        </Dialog>
    );
}

export function ProjectFacts({ p, full = false }) {
    const { L } = useV7();
    const rows = [
        [L.fProg, p.programme || L.tbc],
        [L.fMission, p.mission || rolesText(L, p.roles)],
        [L.fState, stageText(L, p)],
        [L.fSurface, p.surface_m2 ? `${p.surface_m2} m²` : L.tbc],
        [L.fYear, p.period || L.tbc],
    ];
    if (full && p.deliverables?.length) rows.push([L.fDeliverables, p.deliverables.join(' · ')]);
    return (
        <dl className="g2 f-dl" style={{ margin: '24px 0 0', gap: '0 24px' }}>
            {rows.map(([k, v]) => (
                <div key={k}>
                    <dt>{k}</dt>
                    <dd>{v}</dd>
                </div>
            ))}
        </dl>
    );
}

export function Projects({ projects, page = false }) {
    const { L } = useV7();
    const [filter, setFilter] = useState('all');
    const [selId, setSelId] = useState(projects[0]?.id);
    const [fiche, setFiche] = useState(false);
    const [shot, setShot] = useState(null);
    const closeShot = useCallback(() => setShot(null), []);
    const closeFiche = useCallback(() => setFiche(false), []);
    const inCat = (p, f) => f === 'all' || (p.families ?? []).includes(f);
    const shown = projects.filter((p) => inCat(p, filter));
    const sel = shown.find((p) => p.id === selId) ?? shown[0];
    const selImages = useMemo(() => projectImages(sel), [sel]);

    const pick = (f) => {
        setShot(null);
        setFilter(f);
        setSelId((projects.find((p) => inCat(p, f)) ?? projects[0])?.id);
    };

    return (
        <section id="projets" className="f-sec f-wrap" aria-labelledby="s-proj">
            {page ? <h2 className="sr-only" id="s-proj">{L.s4eye}</h2> : <SectionHead id="s-proj" eye={L.s4eye} title={L.s4title} lead={L.s4lead} leadMax={420} />}
            <div role="group" aria-label={L.filterAria} className="chips scroll" style={{ marginBottom: 20 }}>
                {FILTERS.map((f) => (
                    <button key={f} type="button" className="f-chip" aria-pressed={filter === f} onClick={() => pick(f)}>
                        {L.filters[f]} <span style={{ opacity: 0.7 }}>{projects.filter((p) => inCat(p, f)).length}</span>
                    </button>
                ))}
            </div>
            <p className="f-muted" style={{ margin: '-6px 0 20px', fontSize: 14 }}>{L.roleLegend}</p>
            <div aria-live="polite" className="sr-only">
                {shown.length} {L.s4eye.toLowerCase()}
            </div>
            {!sel ? (
                <div className="f-card f-panel" style={{ padding: 32, textAlign: 'center' }}>
                    <p className="f-muted" style={{ margin: '0 0 16px' }}>{L.noProjects}</p>
                    <button type="button" className="f-btn f-btn-sm f-btn-ghost f-btn-inline" onClick={() => pick('all')}>{L.resetFilter}</button>
                </div>
            ) : (
                <div className="gal">
                    <div className="f-card f-panel" style={{ overflow: 'hidden' }}>
                        <ZoomCover key={sel.id} className="f-swap" src={sel.cover_image} title={sel.title} pos={sel.cover_position} sizes="(max-width: 980px) 100vw, 60vw" priority={page} count={selImages.length} onOpen={() => setShot(0)} height={440}>
                            {sel.provisional && <span className="f-pill" style={{ top: 16, left: 16 }}>{L.provisional}</span>}
                        </ZoomCover>
                        <div className="f-pad" style={{ padding: '30px 32px 32px' }}>
                            <div className="chips" style={{ marginBottom: 14 }}>
                                <RoleBadges roles={sel.roles} />
                            </div>
                            <div style={{ display: 'flex', justifyContent: 'space-between', gap: 12, flexWrap: 'wrap', alignItems: 'baseline' }}>
                                <h3 style={{ margin: 0, fontSize: 30 }}>{sel.title}</h3>
                                <span className="f-muted" style={{ fontSize: 15 }}>{sel.location}</span>
                            </div>
                            <dl className="g3 f-dl3" style={{ margin: '22px 0 0', gap: 20 }}>
                                {[[L.fProg, sel.programme || L.tbc], [L.fMission, sel.mission || rolesText(L, sel.roles)], [L.fState, stageText(L, sel)]].map(([k, v]) => (
                                    <div key={k}>
                                        <dt className="f-eye muted">{k}</dt>
                                        <dd style={{ margin: '6px 0 0', lineHeight: 1.55 }}>{v}</dd>
                                    </div>
                                ))}
                            </dl>
                            <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap', marginTop: 24 }}>
                                <button type="button" onClick={() => setFiche(true)} className="f-btn f-btn-sm f-btn-ghost" aria-haspopup="dialog">
                                    {L.ficheCta}
                                    <Icon name="arrow" size={16} stroke={2.2} />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="thumbs">
                        {shown.map((p) => (
                            <button key={p.id} type="button" onClick={() => { setShot(null); setSelId(p.id); }} aria-pressed={p.id === sel.id} className="f-tile" style={{ flex: 'none', display: 'flex', gap: 14, alignItems: 'center', padding: 10, borderRadius: 18, border: `1px solid ${p.id === sel.id ? '#FE5000' : 'var(--v-line)'}`, background: 'var(--v-surface)', color: 'var(--v-text)', cursor: 'pointer', textAlign: 'left', fontFamily: 'Manrope, sans-serif', minWidth: 260 }}>
                                <span className="f-media" style={{ width: 92, height: 72, borderRadius: 12, flex: 'none', display: 'block' }}>
                                    <Img src={p.cover_image} pos={p.cover_position} sizes="92px" />
                                </span>
                                <span style={{ display: 'flex', flexDirection: 'column', gap: 4 }}>
                                    <span style={{ fontFamily: "'Clash Display', sans-serif", fontWeight: 700, fontSize: 17 }}>{p.title}</span>
                                    <span className="f-muted" style={{ fontSize: 13 }}>{p.location}</span>
                                    <span className="f-accent" style={{ fontSize: 12, fontWeight: 700 }}>{rolesText(L, p.roles)}</span>
                                </span>
                            </button>
                        ))}
                    </div>
                </div>
            )}
            {!page && (
                <div style={{ marginTop: 24 }}>
                    <A className="f-textbtn" href="/projets">
                        {L.seeAllProjects}
                        <Icon name="arrow" size={16} stroke={2.2} />
                    </A>
                </div>
            )}
            <ProjectFiche p={fiche ? sel : null} onClose={closeFiche} onZoom={setShot} />
            <Lightbox images={selImages} index={sel ? shot : null} title={sel?.title ?? ''} onIndex={setShot} onClose={closeShot} />
        </section>
    );
}

/* 05 — MÉTHODE (light band) */
export function Method() {
    const { L } = useV7();
    const [open, setOpen] = useState(false);
    return (
        <section id="methode" aria-labelledby="s-meth" style={{ marginTop: 112, background: 'var(--v-alt-bg)', color: 'var(--v-alt-text)', scrollMarginTop: 72 }}>
            <div className="f-wrap f-alt" style={{ paddingTop: 96, paddingBottom: 96 }}>
                <div className="f-head">
                    <div>
                        <div className="f-eye" style={{ color: 'var(--v-alt-accent)' }}>{L.s5eye}</div>
                        <h2 className="f-h2" id="s-meth" style={{ maxWidth: 700 }}>{L.s5title}</h2>
                    </div>
                    <p className="f-lead" style={{ maxWidth: 380, color: 'var(--v-alt-muted)' }}>{L.s5lead}</p>
                </div>
                <ol className="g5" style={{ listStyle: 'none', margin: 0, padding: 0 }}>
                    {L.steps.map(([title, text, what, get], i) => (
                        <li key={title} style={{ paddingTop: 20, borderTop: `2px solid ${i === 0 ? '#FE5000' : 'var(--v-alt-line)'}` }}>
                            <div aria-hidden="true" style={{ fontFamily: "'Clash Display', sans-serif", fontSize: 44, fontWeight: 700, lineHeight: 1, color: i === 0 ? 'var(--v-alt-accent)' : 'var(--v-alt-text)' }}>{String(i + 1).padStart(2, '0')}</div>
                            <h3 style={{ margin: '14px 0 8px', fontSize: 20 }}>
                                <span className="sr-only">{i + 1}. </span>
                                {title}
                            </h3>
                            <p style={{ margin: 0, color: 'var(--v-alt-muted)', fontSize: 15, lineHeight: 1.6 }}>{text}</p>
                            {open && (
                                <div style={{ marginTop: 14, paddingTop: 14, borderTop: '1px solid var(--v-alt-line)', fontSize: 14, lineHeight: 1.6 }}>
                                    <div style={{ fontWeight: 800, letterSpacing: '.06em', fontSize: 12, color: 'var(--v-alt-accent)' }}>{L.mWhat}</div>
                                    <p style={{ margin: '4px 0 10px', color: 'var(--v-alt-muted)' }}>{what}</p>
                                    <div style={{ fontWeight: 800, letterSpacing: '.06em', fontSize: 12, color: 'var(--v-alt-accent)' }}>{L.mGet}</div>
                                    <p style={{ margin: '4px 0 0', color: 'var(--v-alt-muted)' }}>{get}</p>
                                </div>
                            )}
                        </li>
                    ))}
                </ol>
                <button type="button" onClick={() => setOpen(!open)} aria-expanded={open} className="f-btn f-btn-sm" style={{ marginTop: 36, background: 'transparent', color: 'var(--v-alt-text)', border: '1px solid var(--v-alt-line)' }}>
                    {open ? L.methLess : L.methMore}
                    <Icon name={open ? 'up' : 'down'} size={16} stroke={2.2} />
                </button>
            </div>
        </section>
    );
}

/* 06 — ENGAGEMENTS */
export function Commitments() {
    const { L } = useV7();
    return (
        <section id="engagements" className="f-sec f-wrap" aria-labelledby="s-eng">
            <SectionHead id="s-eng" eye={L.s6eye} title={L.s6title} titleMax={680} />
            <div className="g4">
                {L.commits.map(([icon, title, text]) => (
                    <div key={title} className="f-card f-panel f-hover" style={{ padding: 28 }}>
                        <div className="f-icon-tile" style={{ width: 48, height: 48, borderRadius: 14 }}>
                            <Icon name={icon} size={22} />
                        </div>
                        <h3 style={{ margin: '18px 0 8px', fontSize: 21 }}>{title}</h3>
                        <p className="f-muted" style={{ margin: 0, fontSize: 15, lineHeight: 1.6 }}>{text}</p>
                    </div>
                ))}
            </div>
        </section>
    );
}

/* 07 — OFFRES */
export function OfferCard({ o, detailed = false }) {
    const { L } = useV7();
    return (
        <div className="f-card f-hover f-pad" style={{ position: 'relative', padding: 32, background: o.recommended ? 'linear-gradient(160deg, rgba(254,80,0,.2), rgba(254,80,0,.03) 60%), var(--v-surface)' : 'var(--v-surface)', border: `1px solid ${o.recommended ? 'rgba(254,80,0,.6)' : 'var(--v-line)'}`, boxShadow: 'var(--v-shadow)', display: 'flex', flexDirection: 'column', gap: 14 }}>
            {o.recommended && <span style={{ position: 'absolute', top: -13, left: 32, padding: '6px 12px', borderRadius: 999, background: '#FE5000', color: '#0B090B', fontSize: 12, fontWeight: 800, letterSpacing: '.06em' }}>{L.reco}</span>}
            <h3 style={{ margin: 0, fontSize: 26 }}>{o.title}</h3>
            <div className="f-muted" style={{ fontSize: 15 }}>{o.subtitle}</div>
            {detailed && o.audience && <div style={{ fontSize: 14 }}><span className="f-muted">{L.forWho} : </span><strong>{o.audience}</strong></div>}
            <div>
                <div className="f-muted" style={{ fontSize: 13 }}>{L.priceLabel}</div>
                <div style={{ fontFamily: "'Clash Display', sans-serif", fontSize: 36, fontWeight: 700 }}>{o.price_label}</div>
            </div>
            <div className="f-eye muted">{L.included}</div>
            <CheckList items={o.included} />
            {detailed && o.excluded?.length > 0 && (
                <>
                    <div className="f-eye muted" style={{ marginTop: 6 }}>{L.excluded}</div>
                    <ul className="f-list f-muted" style={{ fontSize: 15 }}>
                        {o.excluded.map((x) => (
                            <li key={x}>
                                <Icon name="minus" size={18} style={{ marginTop: 3 }} />
                                {x}
                            </li>
                        ))}
                    </ul>
                </>
            )}
            {detailed && o.price_factors?.length > 0 && (
                <>
                    <div className="f-eye muted" style={{ marginTop: 6 }}>{L.priceFactors}</div>
                    <p className="f-muted" style={{ margin: 0, fontSize: 14, lineHeight: 1.6 }}>{o.price_factors.join(' · ')}</p>
                </>
            )}
            <A className={`f-btn ${o.recommended ? 'f-btn-primary' : 'f-btn-ghost'}`} href={`/mon-projet?besoin=${o.need}&offre=${o.slug}`} style={{ marginTop: 'auto' }}>
                {o.cta}
            </A>
        </div>
    );
}

export function Offers({ offers, page = false }) {
    const { L } = useV7();
    return (
        <section id="offres" className="f-sec f-wrap" aria-labelledby="s-off">
            {page ? <h2 className="sr-only" id="s-off">{L.s7eye}</h2> : <SectionHead center id="s-off" eye={L.s7eye} title={L.s7title} lead={L.s7lead} titleMax={760} />}
            <div className="g3" style={{ alignItems: 'stretch', paddingTop: page ? 16 : 0 }}>
                {offers.map((o) => (
                    <OfferCard key={o.slug} o={o} detailed={page} />
                ))}
            </div>
            <p className="f-muted" style={{ margin: '20px 0 0', textAlign: 'center', fontSize: 14 }}>
                {L.offersNote}{' '}
                <A href="/expertises" style={{ color: 'var(--v-accent-text)', fontWeight: 700 }}>{L.offersOther}</A>
            </p>
        </section>
    );
}

/* 08 — COUP DE POUCE */
export { CdpHomeBlock as CoupDePouce } from './cdp';

export function Abroad() {
    const { L, site } = useV7();
    return (
        <section id="etranger" className="f-sec f-wrap" aria-labelledby="s-abroad">
            <div className="f-card" style={{ position: 'relative', overflow: 'hidden', minHeight: 440, display: 'flex', alignItems: 'center', background: '#0B090B' }}>
                <Img src={img('akwa')} alt={L.s9alt} className="" sizes="100vw" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} />
                <div aria-hidden="true" style={{ position: 'absolute', inset: 0, background: 'linear-gradient(90deg, rgba(11,9,11,.95) 0%, rgba(11,9,11,.8) 55%, rgba(11,9,11,.3) 100%)' }} />
                <div className="f-pad" style={{ position: 'relative', padding: 56, maxWidth: 640, color: '#F7F3EE' }}>
                    <div className="f-eye" style={{ color: '#FE5000' }}>{L.s9eye}</div>
                    <h2 className="f-h2 f-h2s" id="s-abroad">{L.s9title}</h2>
                    <p className="f-lead" style={{ margin: '20px 0 28px', color: 'rgba(247,243,238,.9)' }}>{L.s9lead}</p>
                    <a className="f-btn f-btn-primary" href={waLink(site)}>{L.s9cta}</a>
                </div>
            </div>
        </section>
    );
}

/* 10 — CONSEILS */
export function ArticleCard({ post }) {
    const { L, locale } = useV7();
    const inner = (
        <>
            <div className="f-media" style={{ height: 200 }}>
                <Img src={post.cover_image} pos={post.pos ?? 'center 50%'} sizes="(max-width: 767px) 100vw, 33vw" />
            </div>
            <div style={{ padding: '24px 26px 28px' }}>
                <div style={{ display: 'flex', gap: 8, alignItems: 'center', flexWrap: 'wrap' }}>
                    <span className="f-eye" style={{ fontSize: 12 }}>{post.category?.name ?? post.cat}</span>
                    <span className="f-muted" style={{ fontSize: 12, fontWeight: 700 }}>· {post.published_at ? formatDate(post.published_at, locale) : L.soonShort}</span>
                </div>
                <h3 style={{ margin: '10px 0 0', fontSize: 21, lineHeight: 1.3 }}>{post.title}</h3>
            </div>
        </>
    );
    return (
        <article className="f-card f-panel f-hover f-tile" style={{ overflow: 'hidden' }}>
            {post.slug ? (
                <A href={`/conseils/${post.slug}`} style={{ color: 'inherit', display: 'block' }}>
                    {inner}
                </A>
            ) : (
                inner
            )}
        </article>
    );
}

export function upcomingPosts(L) {
    return L.upcoming.map(([name, pos, cat, title]) => ({ cover_image: img(name), pos, cat, title }));
}

export function Advice({ posts }) {
    const { L } = useV7();
    const list = posts?.length ? posts : upcomingPosts(L);
    return (
        <section id="conseils" className="f-sec f-wrap" aria-labelledby="s-adv">
            <SectionHead id="s-adv" eye={L.s10eye} title={L.s10title} lead={L.s10lead} />
            <div className="g3">
                {list.map((p) => (
                    <ArticleCard key={p.slug ?? p.title} post={p} />
                ))}
            </div>
            {posts?.length > 0 && (
                <div style={{ marginTop: 24 }}>
                    <A className="f-textbtn" href="/conseils">
                        {L.allArticles}
                        <Icon name="arrow" size={16} stroke={2.2} />
                    </A>
                </div>
            )}
        </section>
    );
}

/* 11 — CONTACT */
export function ContactDetails() {
    const { L, site } = useV7();
    const c = site.contact ?? {};
    return (
        <ul style={{ listStyle: 'none', margin: 0, padding: 0, display: 'flex', flexDirection: 'column', gap: 12, fontSize: 16 }}>
            {c.phone && <li><a href={`tel:${c.phone.replace(/\s/g, '')}`} style={{ color: 'var(--v-text)', fontWeight: 700 }}>{c.phone_label} {c.phone}</a></li>}
            {c.phone_secondary && <li><a href={`tel:${c.phone_secondary.replace(/\s/g, '')}`} style={{ color: 'var(--v-text)', fontWeight: 700 }}>{c.phone_secondary_label} {c.phone_secondary}</a></li>}
            {c.email && <li><a href={`mailto:${c.email}`} style={{ color: 'var(--v-text)', fontWeight: 700 }}>{c.email}</a></li>}
            <li><a href={waLink(site)} style={{ color: 'var(--v-accent-text)', fontWeight: 700 }}>WhatsApp →</a></li>
            <li className="f-muted">{L.address}</li>
        </ul>
    );
}

/**
 * « Où nous trouver ? » (additif Contact, 18/11/2026).
 *
 * Shown only once FEIRRY has saved the exact GPS point of the head office in
 * the admin: no approximate or invented position is ever published. The map
 * is loaded on request (performance, and no third-party call before the
 * visitor asks). The page never asks for the visitor's own location: the
 * directions link hands over to Google Maps, in a new tab so that nothing
 * typed in the form is lost.
 */
export function officePoint(contact) {
    const lat = Number.parseFloat(contact?.latitude);
    const lng = Number.parseFloat(contact?.longitude);
    const given = String(contact?.latitude ?? '').trim() !== '' && String(contact?.longitude ?? '').trim() !== '';
    return given && Number.isFinite(lat) && Number.isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180 ? { lat, lng } : null;
}

export function FindUs() {
    const { L, site } = useV7();
    const c = site.contact ?? {};
    const point = officePoint(c);
    if (!point) return null;

    const F = L.find;
    const at = `${point.lat},${point.lng}`;
    const route = `https://www.google.com/maps/dir/?api=1&destination=${at}`;
    const place = c.maps_url || `https://www.google.com/maps/search/?api=1&query=${at}`;
    // Half-width of the view, in degrees: about 500 m across, enough to show the crossroads.
    const d = 0.0022;
    const embed = `https://www.openstreetmap.org/export/embed.html?bbox=${point.lng - d},${point.lat - d * 0.62},${point.lng + d},${point.lat + d * 0.62}&layer=mapnik`;
    const ext = { target: '_blank', rel: 'noopener noreferrer' };

    return (
        <section id="localisation" className="f-sec f-wrap" aria-labelledby="s-find" style={{ paddingTop: 0, paddingBottom: 112 }}>
            <div className="f-head">
                <div style={{ maxWidth: 760 }}>
                    <h2 className="f-h2 f-h2s" id="s-find">{F.title}</h2>
                    <p className="f-lead" style={{ marginTop: 16 }}>{F.lead}</p>
                </div>
            </div>
            <div className="split" style={{ alignItems: 'stretch' }}>
                <div className="f-card f-panel f-pad" style={{ padding: 32, display: 'flex', flexDirection: 'column', gap: 14 }}>
                    <span className="f-badge" style={{ alignSelf: 'flex-start', background: '#FE5000', color: '#0B090B' }}>{F.badge}</span>
                    <h3 style={{ margin: 0, fontSize: 26 }}>{F.place}</h3>
                    <address style={{ fontStyle: 'normal', fontSize: 16, lineHeight: 1.6 }}>
                        {c.address}, {c.city}, {F.country}
                    </address>
                    {c.hours && (
                        <div>
                            <strong style={{ display: 'block', fontSize: 13, letterSpacing: '.08em', textTransform: 'uppercase', color: 'var(--v-muted)' }}>{F.hours}</strong>
                            {String(c.hours).split(' · ').map((line) => (
                                <span key={line} style={{ display: 'block', fontSize: 16, lineHeight: 1.6 }}>{line}</span>
                            ))}
                        </div>
                    )}
                    <p className="f-muted" style={{ margin: 0, lineHeight: 1.6 }}>{F.reach}</p>
                    {c.secondary_presence && <p className="f-muted" style={{ margin: 0 }}>{F.also} {c.secondary_presence}.</p>}
                    <div style={{ display: 'flex', flexWrap: 'wrap', gap: 10, marginTop: 'auto', paddingTop: 10 }}>
                        <a className="f-btn f-btn-primary" href={route} {...ext}>
                            <Icon name="nav" size={18} stroke={2.2} />
                            {F.route}
                            <span className="sr-only"> {F.newTab}</span>
                        </a>
                        <a className="f-btn f-btn-ghost" href={place} {...ext}>
                            {F.open}
                            <span className="sr-only"> {F.newTab}</span>
                        </a>
                    </div>
                    <p className="f-muted" style={{ margin: 0, fontSize: 13, lineHeight: 1.55 }}>{F.routeNote}</p>
                </div>
                <div>
                    {/* The map is a picture here, not a control: it cannot be dragged, so the
                        orange pin stays on the building, and one click anywhere opens the route. */}
                    <div className="f-card find-map">
                        <iframe className="find-map-frame" title={F.mapTitle} src={embed} loading="lazy" referrerPolicy="no-referrer" tabIndex={-1} />
                        <a className="find-map-link" href={route} {...ext} aria-label={`${F.route} : ${F.marker} ${F.newTab}`}>
                            <span className="f-pill" style={{ top: 16, left: 16 }}>{F.marker}</span>
                            <span className="find-pin-spot" aria-hidden="true" />
                            <span className="find-pin" aria-hidden="true" />
                            <span className="f-pill find-cta" style={{ right: 16, bottom: 16 }} aria-hidden="true">
                                <Icon name="nav" size={14} stroke={2.2} />
                                {F.route}
                            </span>
                        </a>
                    </div>
                    <p className="f-muted" style={{ margin: '10px 0 0', fontSize: 14 }}>
                        {F.tap}{' '}
                        <a href="https://www.openstreetmap.org/copyright" {...ext} style={{ color: 'var(--v-accent-text)', fontWeight: 700 }}>{F.osm}</a>
                    </p>
                </div>
            </div>
        </section>
    );
}

export function Contact({ offers, preset, page = false }) {
    const { L, site } = useV7();
    return (
        <section id="contact" className="f-sec f-wrap" aria-labelledby="s-contact" style={{ paddingBottom: page && officePoint(site.contact) ? 88 : 112, paddingTop: page ? 40 : undefined }}>
            <div className="split" style={{ alignItems: 'start' }}>
                <div>
                    {!page && (
                        <>
                            <div className="f-eye">{L.s11eye}</div>
                            <h2 className="f-h2" id="s-contact">
                                {L.s11a} <span style={{ color: '#FE5000' }}>{L.s11b}</span>
                            </h2>
                            <p className="f-lead" style={{ margin: '20px 0 28px' }}>{L.s11lead}</p>
                        </>
                    )}
                    {page && <h2 id="s-contact" style={{ margin: '0 0 18px', fontSize: 22 }}>{L.contactDirect}</h2>}
                    <ContactDetails />
                </div>
                <ProjectWizard offers={offers} preset={preset} />
            </div>
        </section>
    );
}
