import { A, CheckList, Icon, Img, PageHead, useV7 } from '@/v7/core';
import { ExpertiseCard } from '@/v7/sections';

export default function Expertise({ expertise: e, others }) {
    const { L } = useV7();
    const blocks = [
        [L.expSituations, e.situations],
        [L.expIncl, e.features],
        [L.expDeliverables, e.deliverables],
    ].filter(([, items]) => items?.length);

    return (
        <>
            <PageHead crumbs={[[L.nav.expertises, '/expertises'], [e.title]]} eye={e.family} title={e.title} lead={e.excerpt}>
                <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap', marginTop: 28 }}>
                    <A className="f-btn f-btn-primary" href={`/mon-projet?besoin=${e.need}`}>{L.expAsk}</A>
                    <A className="f-btn f-btn-ghost" href="/expertises">{L.allExpertises}</A>
                </div>
            </PageHead>
            <section className="f-sec f-wrap" style={{ paddingTop: 48 }}>
                <div className="split" style={{ alignItems: 'start' }}>
                    <div>
                        <p className="f-lead" style={{ color: 'var(--v-text)', fontSize: 19 }}>{e.body}</p>
                        {blocks.map(([title, items]) => (
                            <div key={title} style={{ marginTop: 32 }}>
                                <h2 style={{ margin: '0 0 14px', fontSize: 24 }}>{title}</h2>
                                <CheckList items={items} size={16} />
                            </div>
                        ))}
                        <div style={{ marginTop: 32 }}>
                            <h2 style={{ margin: '0 0 14px', fontSize: 24 }}>{L.expProcess}</h2>
                            <ol className="f-list" style={{ gap: 12 }}>
                                {L.steps.slice(0, 3).concat(L.steps.slice(4)).map(([t, d], i) => (
                                    <li key={t}>
                                        <span className="f-accent" style={{ fontFamily: "'Clash Display', sans-serif", fontWeight: 700, minWidth: 28 }}>{String(i + 1).padStart(2, '0')}</span>
                                        <span><strong>{t}</strong> <span className="f-muted">— {d}</span></span>
                                    </li>
                                ))}
                            </ol>
                        </div>
                        {e.limits && (
                            <div className="f-card" style={{ marginTop: 32, padding: 22, border: '1.5px dashed var(--v-line-strong)' }}>
                                <h2 className="f-eye muted" style={{ margin: 0, fontFamily: 'Manrope, sans-serif' }}>{L.expLimits}</h2>
                                <p className="f-muted" style={{ margin: '8px 0 0', lineHeight: 1.6 }}>{e.limits}</p>
                            </div>
                        )}
                    </div>
                    <div style={{ position: 'sticky', top: 96 }}>
                        <div className="f-card f-media" style={{ height: 420 }}>
                            <Img src={e.cover_image} alt={`${L.provisional} : ${e.title}`} pos={e.cover_position} sizes="(max-width: 980px) 100vw, 45vw" priority />
                            <span className="f-pill" style={{ left: 16, bottom: 16 }}>{L.provisional}</span>
                        </div>
                        <div className="f-card f-panel" style={{ marginTop: 16, padding: 24 }}>
                            <h2 style={{ margin: '0 0 12px', fontSize: 20 }}>{L.expFaqTitle}</h2>
                            {L.expFaq.map(([q, a]) => (
                                <details key={q} style={{ borderTop: '1px solid var(--v-line)', padding: '12px 0' }}>
                                    <summary style={{ cursor: 'pointer', fontWeight: 700, minHeight: 32 }}>{q}</summary>
                                    <p className="f-muted" style={{ margin: '8px 0 0', lineHeight: 1.6 }}>{a}</p>
                                </details>
                            ))}
                            <A className="f-btn f-btn-primary" href={`/mon-projet?besoin=${e.need}`} style={{ marginTop: 12, width: '100%' }}>
                                {L.expAsk}
                                <Icon name="arrow" size={18} />
                            </A>
                        </div>
                    </div>
                </div>
            </section>
            <section className="f-sec f-wrap" style={{ paddingBottom: 112 }} aria-labelledby="others">
                <h2 id="others" className="f-h2 f-h2s" style={{ marginBottom: 28 }}>{L.expOthers}</h2>
                <div className="g3">
                    {others.slice(0, 3).map((o, i) => (
                        <ExpertiseCard key={o.slug} e={o} index={i} />
                    ))}
                </div>
            </section>
        </>
    );
}
