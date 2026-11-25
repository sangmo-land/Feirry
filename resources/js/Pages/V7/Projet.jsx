import { useCallback, useMemo, useState } from 'react';
import { A, Icon, Img, PageHead, RoleBadges, useV7 } from '@/v7/core';
import { GalleryGrid, Lightbox, ZoomCover, projectImages } from '@/v7/gallery';
import { ProjectFacts } from '@/v7/sections';

export default function Projet({ project: p, next }) {
    const { L } = useV7();
    const hasGallery = (p.gallery?.length ?? 0) > 0;
    // One list for the whole page: the cover, then the gallery. Any picture opens it.
    const images = useMemo(() => projectImages(p), [p]);
    const fromCover = p.cover_image ? 1 : 0;
    const [shot, setShot] = useState(null);
    const closeShot = useCallback(() => setShot(null), []);
    const need = (p.roles ?? []).includes('build') ? 'construction' : (p.roles ?? []).includes('follow') ? 'suivi' : 'conception';
    return (
        <>
            <PageHead crumbs={[[L.nav.projets, '/projets'], [p.title]]} eye={L.ficheEye} title={p.title} lead={p.location}>
                <div className="chips" style={{ marginTop: 18 }}>
                    <RoleBadges roles={p.roles} />
                </div>
            </PageHead>
            <section className="f-wrap" style={{ paddingTop: 32 }}>
                <ZoomCover className="f-card" src={p.cover_image} title={p.title} pos={p.cover_position} sizes="100vw" priority count={images.length} onOpen={() => setShot(0)} height="min(62vw, 560px)" style={{ minHeight: 260 }}>
                    {p.provisional && <span className="f-pill" style={{ left: 16, bottom: 16 }}>{L.provisional}</span>}
                </ZoomCover>
            </section>
            {hasGallery && (
                <section className="f-wrap" style={{ paddingTop: 32 }} aria-labelledby="proj-gal">
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', gap: 12, flexWrap: 'wrap', marginBottom: 14 }}>
                        <h2 id="proj-gal" style={{ margin: 0, fontSize: 26 }}>{L.galEye}</h2>
                        <span className="f-muted" style={{ fontSize: 14 }}>{p.gallery.length} {p.gallery.length > 1 ? L.galImages : L.galImage} · {L.galHint}</span>
                    </div>
                    <GalleryGrid images={p.gallery} total={images.length} onOpen={(i) => setShot(i + fromCover)} />
                </section>
            )}
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
                        {!hasGallery && (
                            <>
                                <div className="f-eye muted">{L.galEye}</div>
                                <div className="g2" style={{ marginTop: 10, gap: 10 }}>
                                    <div className="f-media" style={{ height: 150, borderRadius: 14 }}>
                                        <Img src={p.cover_image} pos={p.cover_position} sizes="25vw" alt="" />
                                    </div>
                                    <div style={{ height: 150, borderRadius: 14, border: '1.5px dashed var(--v-line-strong)', display: 'flex', alignItems: 'center', justifyContent: 'center', textAlign: 'center', padding: 10, boxSizing: 'border-box', color: 'var(--v-muted)', fontSize: 13, fontWeight: 600 }}>{L.galSite}</div>
                                </div>
                            </>
                        )}
                        <div className="f-card f-panel" style={{ marginTop: hasGallery ? 0 : 20, padding: 24 }}>
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
            <Lightbox images={images} index={shot} title={p.title} onIndex={setShot} onClose={closeShot} />
        </>
    );
}
