import { A, PageHead, useV7 } from '@/v7/core';
import { Expertises as Grid, Method } from '@/v7/sections';

export default function Expertises({ expertises }) {
    const { L } = useV7();
    return (
        <>
            <PageHead crumbs={[[L.nav.expertises]]} eye={L.s3eye} title={L.expPageTitle} lead={L.s3lead} />
            <Grid expertises={expertises} page />
            <Method />
            <section className="f-sec f-wrap" style={{ paddingBottom: 112, textAlign: 'center' }}>
                <h2 className="f-h2 f-h2s" style={{ maxWidth: 760, margin: '0 auto' }}>{L.offersCustomTitle}</h2>
                <p className="f-lead" style={{ maxWidth: 620, margin: '16px auto 0' }}>{L.offersCustom}</p>
                <A className="f-btn f-btn-primary" href="/mon-projet" style={{ marginTop: 28 }}>{L.offersCustomCta}</A>
            </section>
        </>
    );
}
