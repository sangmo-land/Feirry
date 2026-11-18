import { CdpConfirmation } from '@/v7/cdp';

export default function CoupDePouceConfirmation({ reference, message, waiting, slot, mode }) {
    return (
        <div className="f-wrap f-pagehead" style={{ paddingBottom: 112 }}>
            <CdpConfirmation reference={reference} message={message} waiting={waiting} slot={slot} mode={mode} />
        </div>
    );
}
