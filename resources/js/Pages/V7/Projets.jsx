import { A, PageHead, useV7 } from '@/v7/core';
import { Projects } from '@/v7/sections';

export default function Projets({ projects }) {
    const { L } = useV7();
    return (
        <>
            <PageHead crumbs={[[L.nav.projets]]} eye={L.s4eye} title={L.projPageTitle} lead={L.projPageLead} />
            <Projects projects={projects} page />
            <section className="f-sec f-wrap" style={{ paddingBottom: 112, textAlign: 'center' }}>
                <h2 className="f-h2 f-h2s">{L.projectCta}</h2>
                <A className="f-btn f-btn-primary" href="/mon-projet" style={{ marginTop: 24 }}>{L.ctaPrimary}</A>
            </section>
        </>
    );
}
