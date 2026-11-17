import { A, Icon, Img, PageHead, RoleBadges, useV7 } from '@/v7/core';
import { ProjectFacts } from '@/v7/sections';

export default function Projet({ project: p, next }) {
    const { L } = useV7();
    const need = (p.roles ?? []).includes('build') ? 'construction' : (p.roles ?? []).includes('follow') ? 'suivi' : 'conception';
    return (
        <>
            <PageHead crumbs={[[L.nav.projets, '/projets'], [p.title]]} eye={L.ficheEye} title={p.title} lead={p.location}>
                <div className="chips" style={{ marginTop: 18 }}>
                    <RoleBadges roles={p.roles} />
                </div>
            </PageHead>
            <section className="f-wrap" style={{ paddingTop: 32 }}>
                <div className="f-card f-media" style={{ height: 'min(62vw, 560px)', minHeight: 260 }}>
                    <Img src={p.cover_image} alt={`${L.provisional} : ${p.title}`} pos={p.cover_position} sizes="100vw" priority />
                    {p.provisional && <span className="f-pill" style={{ left: 16, bottom: 16 }}>{L.provisional}</span>}
                </div>
            </section>
            <section className="f-sec f-wrap" style={{ paddingTop: 48 }}>
                <div className="split" style={{ alignItems: 'start' }}>
                    <div>
                        <h2 style={{ margin: 0, fontSize: 26 }}>{L.fDescription}</h2>
                        {p.body ? (
                            <div className="f-prose" style={{ marginTop: 14 }} dangerouslySetInnerHTML={{ __html: p.body }} />
                        ) : (
                            <p className="f-muted" style={{ marginTop: 12 }}>{L.toWrite}</p>
                        )}
                        <ProjectFacts p={p} full />
                        <div className="f-card" style={{ marginTop: 24, padding: 18, border: '1.5px dashed var(--v-line-strong)' }}>
                            <div className="f-eye muted">{L.cmpEye}</div>
                            <p className="f-muted" style={{ margin: '6px 0 0', lineHeight: 1.55 }}>{L.cmpText}</p>
                        </div>
                        <p className="f-muted" style={{ margin: '16px 0 0', fontSize: 14 }}>{L.ficheNote}</p>
                    </div>
                    <div>
                        <div className="f-eye muted">{L.galEye}</div>
                        <div className="g2" style={{ marginTop: 10, gap: 10 }}>
                            {(p.gallery?.length ? p.gallery : [p.cover_image]).slice(0, 4).map((src, i) => (
                                <div key={src + i} className="f-media" style={{ height: 150, borderRadius: 14 }}>
                                    <Img src={src} pos={p.cover_position} sizes="25vw" alt={i === 0 ? '' : `${p.title} ${i + 1}`} />
                                </div>
                            ))}
                            {!p.gallery?.length && (
                                <div style={{ height: 150, borderRadius: 14, border: '1.5px dashed var(--v-line-strong)', display: 'flex', alignItems: 'center', justifyContent: 'center', textAlign: 'center', padding: 10, boxSizing: 'border-box', color: 'var(--v-muted)', fontSize: 13, fontWeight: 600 }}>{L.galSite}</div>
                            )}
                        </div>
                        <div className="f-card f-panel" style={{ marginTop: 20, padding: 24 }}>
                            <h2 style={{ margin: '0 0 14px', fontSize: 22 }}>{L.projectCta}</h2>
                            <A className="f-btn f-btn-primary" href={`/mon-projet?besoin=${need}`} style={{ width: '100%' }}>{L.ctaPrimary}</A>
                        </div>
                    </div>
                </div>
            </section>
            <nav className="f-wrap" aria-label={L.nav.projets} style={{ display: 'flex', justifyContent: 'space-between', gap: 16, flexWrap: 'wrap', paddingBottom: 112, paddingTop: 24 }}>
                <A className="f-textbtn" href="/projets">
                    <Icon name="arrowL" size={16} />
                    {L.backProjects}
                </A>
                {next && (
                    <A className="f-textbtn" href={`/projets/${next.slug}`}>
                        {L.nextProject} : {next.title}
                        <Icon name="arrow" size={16} />
                    </A>
                )}
            </nav>
        </>
    );
}
