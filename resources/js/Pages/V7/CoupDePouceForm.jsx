import { A } from '@/v7/core';
import { CdpForm, StateBadge, useCdp } from '@/v7/cdp';

export default function CoupDePouceForm({ path, campaign, limits, slots }) {
    const { C, L } = useCdp();
    const F = C.form;
    return (
        <div className="f-wrap f-pagehead" style={{ paddingBottom: 112 }}>
            <nav aria-label={L.crumbs} className="f-crumbs">
                <A href="/">{L.nav.home}</A>
                <span aria-hidden="true">/</span>
                <A href="/coup-de-pouce">{L.nav.cdp}</A>
                <span aria-hidden="true">/</span>
                <span aria-current="page" style={{ color: 'var(--v-text)' }}>{F.titles[path]}</span>
            </nav>
            <div className="split" style={{ alignItems: 'start' }}>
                <div>
                    <StateBadge campaign={campaign} />
                    <h1 className="f-h2" style={{ marginTop: 16 }}>{F.titles[path]}</h1>
                    <p className="f-lead" style={{ marginTop: 14, maxWidth: 480 }}>{F.intro[path]}</p>
                    <p className="f-muted" style={{ marginTop: 14, maxWidth: 480, lineHeight: 1.6 }}>{C.cards[path].text}</p>
                </div>
                <CdpForm path={path} campaign={campaign} limits={limits} slots={slots ?? []} />
            </div>
        </div>
    );
}
