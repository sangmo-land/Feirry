import { PageHead, useV7 } from '@/v7/core';

/*
 * Provisional legal pages (CDC §02: published after legal validation).
 * Only verified company data is used; open points are stated as such.
 */
export default function Legal({ document }) {
    const { L, fr, site } = useV7();
    const c = site.contact ?? {};
    const g = site.legal ?? {};
    const notice = document === 'notice';

    const NOTICE = fr
        ? [
              ['Éditeur du site', `FEIRRY Construction Designs — ${c.address}, ${c.city} (Cameroun). ${g.rccm ? `RCCM ${g.rccm}. ` : ''}${g.niu ? `NIU ${g.niu}. ` : ''}Téléphone : ${c.phone}${c.phone_secondary ? ` / ${c.phone_secondary}` : ''}. E-mail : ${c.email}.`],
              ['Directeur de la publication', 'Le représentant légal de FEIRRY Construction Designs.'],
              ['Hébergement', 'Les coordonnées de l’hébergeur seront précisées ici après confirmation.'],
              ['Propriété intellectuelle', 'Les textes, rendus 3D, photographies, plans et le logo FEIRRY sont la propriété de FEIRRY Construction Designs ou de leurs auteurs. Toute reproduction sans autorisation écrite est interdite. Les visuels marqués « provisoire » sont susceptibles d’être remplacés.'],
              ['Contenus', 'Les informations du site sont fournies à titre indicatif. Les prestations, prix et délais sont fixés par devis et contrat signés.'],
          ]
        : [
              ['Publisher', `FEIRRY Construction Designs — ${c.address}, ${c.city} (Cameroon). ${g.rccm ? `Trade register ${g.rccm}. ` : ''}${g.niu ? `Tax ID ${g.niu}. ` : ''}Phone: ${c.phone}${c.phone_secondary ? ` / ${c.phone_secondary}` : ''}. Email: ${c.email}.`],
              ['Publication director', 'The legal representative of FEIRRY Construction Designs.'],
              ['Hosting', 'The hosting provider’s details will be given here once confirmed.'],
              ['Intellectual property', 'Texts, 3D renders, photographs, drawings and the FEIRRY logo belong to FEIRRY Construction Designs or their authors. Any reproduction without written permission is prohibited. Visuals marked “provisional” may be replaced.'],
              ['Content', 'Information on this site is indicative. Services, prices and schedules are set by a signed quote and contract.'],
          ];

    const PRIVACY = fr
        ? [
              ['Données collectées', 'Via les formulaires « Mon projet » et « Coup de Pouce » : nom, téléphone et/ou e-mail, informations sur votre projet et, si vous le souhaitez, des documents (plans, titre foncier, photos).'],
              ['Finalité', 'Ces données servent à traiter votre demande et à vous recontacter à son sujet. Elles ne sont ni vendues ni transmises à des tiers. Vous ne recevez d’informations sur les services FEIRRY que si vous avez coché la case facultative prévue à cet effet ; vous pouvez retirer cet accord à tout moment.'],
              ['Le Coup de Pouce FEIRRY', 'Les plans, photos et observations transmis pour une consultation ou une visite restent privés, réservés à l’équipe FEIRRY chargée du dossier, et ne sont jamais publiés. Toute publication de photos, vidéos ou témoignages exige une autorisation distincte, écrite et révocable. Refuser une option facultative (informations commerciales, photos, témoignage) ne change rien à l’accès au programme ni à la qualité de l’intervention.'],
              ['Accès et sécurité', 'Les demandes et documents sont stockés sur un espace non public, accessible aux seuls membres habilités de l’équipe FEIRRY. Les échanges avec le site sont chiffrés (HTTPS).'],
              ['Durée de conservation', 'Les demandes sont conservées le temps nécessaire à leur traitement et au suivi commercial ; la durée précise sera indiquée ici après validation.'],
              ['Vos droits', `Vous pouvez demander l’accès, la rectification ou la suppression de vos données en écrivant à ${c.email}.`],
              ['Cookies', 'Le site n’utilise pas de cookies publicitaires ni d’outil de mesure d’audience à ce jour. Votre choix de thème et de langue est enregistré uniquement dans votre navigateur.'],
          ]
        : [
              ['Data collected', 'Through the “My project” and “Coup de Pouce” forms: name, phone and/or email, information about your project and, if you wish, documents (drawings, land title, photos).'],
              ['Purpose', 'This data is used to process your request and contact you about it. It is neither sold nor passed on to third parties. You only receive information about FEIRRY’s services if you ticked the optional box provided for this; you can withdraw this consent at any time.'],
              ['Coup de Pouce FEIRRY', 'Drawings, photos and observations sent for a consultation or a visit stay private, reserved for the FEIRRY team handling the file, and are never published. Any publication of photos, videos or testimonials requires a separate, written and revocable authorisation. Declining an optional choice (marketing information, photos, testimonial) does not affect access to the programme or the quality of the service.'],
              ['Access and security', 'Requests and documents are stored in a non-public space, accessible only to authorised members of the FEIRRY team. Exchanges with the site are encrypted (HTTPS).'],
              ['Retention', 'Requests are kept for as long as needed to process them and follow up; the exact period will be stated here once validated.'],
              ['Your rights', `You can ask to access, correct or delete your data by writing to ${c.email}.`],
              ['Cookies', 'The site uses no advertising cookies and no audience measurement tool at present. Your theme and language choice is stored only in your browser.'],
          ];

    const title = notice ? L.legalNotice : L.legalPrivacy;
    return (
        <>
            <PageHead crumbs={[[title]]} eye="FEIRRY" title={title} lead={L.legalDraft} />
            <section className="f-wrap" style={{ paddingTop: 40, paddingBottom: 112 }}>
                <div className="f-prose" style={{ maxWidth: 760 }}>
                    {(notice ? NOTICE : PRIVACY).map(([h, p]) => (
                        <div key={h}>
                            <h2>{h}</h2>
                            <p>{p}</p>
                        </div>
                    ))}
                </div>
            </section>
        </>
    );
}
