import { Crumbs, useV7 } from '@/v7/core';
import { CoupDePouce as Block } from '@/v7/sections';

export default function CoupDePouce({ campaign }) {
    const { L } = useV7();
    return (
        <>
            <div className="f-wrap" style={{ paddingTop: 40 }}>
                <Crumbs items={[[L.nav.cdp]]} />
            </div>
            <Block campaign={campaign} page />
            <section className="f-sec f-wrap" aria-labelledby="cdp-how">
                <h2 id="cdp-how" className="f-h2 f-h2s" style={{ marginBottom: 28 }}>{L.cdpHow}</h2>
                <ol className="g3" style={{ listStyle: 'none', margin: 0, padding: 0 }}>
                    {L.cdpHowSteps.map((s, i) => (
                        <li key={s} className="f-card f-panel" style={{ padding: 28 }}>
                            <div className="f-accent" style={{ fontFamily: "'Clash Display', sans-serif", fontSize: 40, fontWeight: 700, lineHeight: 1 }}>{String(i + 1).padStart(2, '0')}</div>
                            <p style={{ margin: '14px 0 0', lineHeight: 1.6 }}>{s}</p>
                        </li>
                    ))}
                </ol>
            </section>
            {campaign.terms && (
                <section className="f-sec f-wrap" aria-labelledby="cdp-terms">
                    <h2 id="cdp-terms" className="f-h2 f-h2s">{L.cdpInfo.terms}</h2>
                    <div className="f-prose" style={{ marginTop: 18, whiteSpace: 'pre-line' }}>{campaign.terms}</div>
                </section>
            )}
            <div style={{ height: 112 }} />
        </>
    );
}
