import { A, PageHead, useV7 } from '@/v7/core';
import { Offers } from '@/v7/sections';

export default function Offres({ offers }) {
    const { L } = useV7();
    return (
        <>
            <PageHead crumbs={[[L.nav.offres]]} eye={L.s7eye} title={L.offPageTitle} lead={L.s7lead} />
            <Offers offers={offers} page />
            <section className="f-sec f-wrap" style={{ paddingBottom: 112 }}>
                <div className="f-card f-panel f-pad" style={{ padding: 40, display: 'flex', gap: 24, alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap' }}>
                    <div style={{ maxWidth: 640 }}>
                        <h2 style={{ margin: 0, fontSize: 28 }}>{L.offersCustomTitle}</h2>
                        <p className="f-muted" style={{ margin: '10px 0 0', lineHeight: 1.6 }}>{L.offersCustom}</p>
                    </div>
                    <A className="f-btn f-btn-primary" href="/mon-projet?besoin=suivi">{L.offersCustomCta}</A>
                </div>
            </section>
        </>
    );
}
