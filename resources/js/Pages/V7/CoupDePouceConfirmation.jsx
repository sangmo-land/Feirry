import { CdpConfirmation } from '@/v7/cdp';

export default function CoupDePouceConfirmation({ reference, message }) {
    return (
        <div className="f-wrap f-pagehead" style={{ paddingBottom: 112 }}>
            <CdpConfirmation reference={reference} message={message} />
        </div>
    );
}
