import { A, PageHead, useV7 } from '@/v7/core';
import { Abroad, Commitments, Difference, Method } from '@/v7/sections';

export default function Feirry() {
    const { L } = useV7();
    const blocks = [
        [L.approachTitle, L.approach],
        [L.teamTitle, L.team],
        [L.zoneTitle, L.zone],
    ];
    return (
        <>
            <PageHead crumbs={[[L.nav.feirry]]} eye={L.whoTitle} title={L.feirryTitle} lead={L.feirryLead} />
            <Difference />
            <section className="f-sec f-wrap" aria-label={L.approachTitle}>
                <div className="g3">
                    {blocks.map(([t, d]) => (
                        <div key={t} className="f-card f-panel" style={{ padding: 28 }}>
                            <h2 style={{ margin: '0 0 10px', fontSize: 22 }}>{t}</h2>
                            <p className="f-muted" style={{ margin: 0, lineHeight: 1.65 }}>{d}</p>
                        </div>
                    ))}
                </div>
            </section>
            <Commitments />
            <Method />
            <Abroad />
            <section className="f-sec f-wrap" style={{ paddingBottom: 112, textAlign: 'center' }}>
                <h2 className="f-h2 f-h2s" style={{ maxWidth: 760, margin: '0 auto' }}>
                    {L.s11a} <span className="f-orange">{L.s11b}</span>
                </h2>
                <div style={{ display: 'flex', gap: 12, justifyContent: 'center', flexWrap: 'wrap', marginTop: 28 }}>
                    <A className="f-btn f-btn-primary" href="/mon-projet">{L.ctaPrimary}</A>
                    <A className="f-btn f-btn-ghost" href="/expertises">{L.ctaSecondary}</A>
                </div>
            </section>
        </>
    );
}
