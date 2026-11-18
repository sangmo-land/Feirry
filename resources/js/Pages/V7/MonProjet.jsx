import { PageHead, useV7 } from '@/v7/core';
import { Contact } from '@/v7/sections';

export default function MonProjet({ offers, preset }) {
    const { L } = useV7();
    return (
        <>
            <PageHead crumbs={[[L.ctaHeader]]} eye={L.s11eye} title={L.projectPageTitle} lead={L.projectPageLead} />
            <Contact offers={offers} preset={preset} page />
        </>
    );
}
