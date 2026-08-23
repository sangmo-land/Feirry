import { useSite } from '@/Hooks';
import { Section } from '@/Components/Ui';
import { Reveal } from '@/Components/Motion';
import { PageHero } from '@/Sections/Hero';
import { Crumb, CtaBand } from '@/Sections/Shared';

export default function Legal({ document }) {
    const { t, site } = useSite();

    const isPrivacy = document === 'privacy';
    const title = isPrivacy ? t('footer.privacy') : t('footer.legal');

    return (
        <>
            <PageHero
                title={title}
                breadcrumb={
                    <>
                        <Crumb href="/">{t('nav.home')}</Crumb>
                        <Crumb last>{title}</Crumb>
                    </>
                }
            />

            <Section size="lg">
                <div className="shell">
                    <Reveal className="mx-auto max-w-3xl">
                        <div className="prose prose-brand max-w-none prose-headings:font-display prose-headings:tracking-[-0.03em] prose-h2:mt-12 prose-h2:text-2xl">
                            {isPrivacy ? <Privacy site={site} /> : <Notice site={site} />}
                        </div>
                    </Reveal>
                </div>
            </Section>

            <CtaBand />
        </>
    );
}

function Notice({ site }) {
    return (
        <>
            <h2>Éditeur du site</h2>
            <p>
                <strong>{site.legal_name ?? site.brand?.legal_name}</strong>
                <br />
                {site.contact?.address}, {site.contact?.district}
                <br />
                {site.contact?.postal_box} — {site.contact?.city}, {site.contact?.country}
                <br />
                Téléphone : {site.contact?.phone}
                <br />
                Courriel : {site.contact?.email}
            </p>

            <h2>Informations légales</h2>
            <ul>
                <li>Registre du commerce et du crédit mobilier : {site.legal?.rccm}</li>
                <li>Numéro identifiant unique : {site.legal?.niu}</li>
                <li>Capital social : {site.legal?.capital}</li>
                <li>Assurance responsabilité civile décennale : {site.legal?.insurer}</li>
            </ul>

            <h2>Directeur de la publication</h2>
            <p>
                Le directeur de la publication est le représentant légal de {site.brand?.name}, joignable à
                l’adresse {site.contact?.email}.
            </p>

            <h2>Hébergement</h2>
            <p>
                Le site est hébergé par un prestataire technique dont les coordonnées peuvent être obtenues sur
                simple demande écrite adressée à {site.contact?.email}.
            </p>

            <h2>Propriété intellectuelle</h2>
            <p>
                L’ensemble des contenus présents sur ce site — textes, photographies de chantiers, plans,
                identité visuelle et code source — est la propriété de {site.brand?.name}, sauf mention
                contraire. Toute reproduction, représentation ou adaptation, totale ou partielle, sans
                autorisation écrite préalable est interdite.
            </p>
            <p>
                Les photographies de réalisations publiées le sont avec l’accord des maîtres d’ouvrage
                concernés. Certaines images d’illustration proviennent de banques d’images sous licence.
            </p>

            <h2>Responsabilité</h2>
            <p>
                Les informations techniques, fourchettes de prix et délais publiés sur ce site sont donnés à
                titre indicatif. Ils ne constituent pas une offre contractuelle. Seul un devis signé engage{' '}
                {site.brand?.name}.
            </p>

            <h2>Liens externes</h2>
            <p>
                Ce site peut contenir des liens vers des sites tiers. {site.brand?.name} n’exerce aucun
                contrôle sur ces sites et décline toute responsabilité quant à leur contenu.
            </p>

            <h2>Droit applicable</h2>
            <p>
                Le présent site est soumis au droit camerounais. Tout litige relatif à son utilisation relève
                de la compétence des tribunaux de Douala.
            </p>
        </>
    );
}

function Privacy({ site }) {
    return (
        <>
            <p className="lead">
                Cette politique explique quelles données personnelles {site.brand?.name} collecte via ce site,
                pourquoi elle les collecte, et comment vous pouvez exercer vos droits.
            </p>

            <h2>Responsable du traitement</h2>
            <p>
                {site.legal_name ?? site.brand?.legal_name}, {site.contact?.address},{' '}
                {site.contact?.district}, {site.contact?.city}, {site.contact?.country}. Contact :{' '}
                {site.contact?.email}.
            </p>

            <h2>Données collectées</h2>
            <p>Nous ne collectons que les données que vous nous transmettez volontairement :</p>
            <ul>
                <li>
                    <strong>Formulaire de devis</strong> : nom, société, adresse e-mail, téléphone, nature et
                    localisation du projet, budget et échéance envisagés, pièces jointes éventuelles.
                </li>
                <li>
                    <strong>Formulaire de contact</strong> : nom, adresse e-mail, téléphone, objet et contenu
                    du message, ainsi que l’adresse IP d’envoi à des fins de prévention des abus.
                </li>
                <li>
                    <strong>Candidature</strong> : nom, adresse e-mail, téléphone, CV, lettre de motivation,
                    profil LinkedIn et années d’expérience.
                </li>
                <li>
                    <strong>Newsletter</strong> : adresse e-mail et langue de préférence.
                </li>
            </ul>

            <h2>Finalités et bases légales</h2>
            <p>
                Ces données sont traitées pour répondre à votre demande, établir un devis, instruire une
                candidature ou vous adresser nos publications. La base légale est votre consentement, ou
                l’exécution de mesures précontractuelles prises à votre demande.
            </p>

            <h2>Durée de conservation</h2>
            <ul>
                <li>Demandes de devis et messages de contact : trois ans à compter du dernier échange.</li>
                <li>Candidatures non retenues : douze mois, sauf demande de suppression anticipée.</li>
                <li>Abonnements à la newsletter : jusqu’à votre désabonnement.</li>
            </ul>

            <h2>Destinataires</h2>
            <p>
                Vos données sont accessibles aux seuls collaborateurs de {site.brand?.name} chargés du
                traitement de votre demande. Elles ne sont ni vendues, ni louées, ni transmises à des fins
                commerciales à des tiers.
            </p>

            <h2>Cookies</h2>
            <p>
                Ce site n’utilise pas de cookies publicitaires ni de traceurs tiers. Seul un cookie de session
                strictement nécessaire au fonctionnement des formulaires et à la mémorisation de votre choix de
                langue est déposé.
            </p>

            <h2>Vos droits</h2>
            <p>
                Vous disposez d’un droit d’accès, de rectification, d’effacement et d’opposition sur vos
                données. Pour l’exercer, écrivez à {site.contact?.email} en précisant votre demande. Nous
                répondons sous trente jours.
            </p>

            <h2>Sécurité</h2>
            <p>
                Les échanges avec ce site sont chiffrés. Les CV déposés via le formulaire de candidature sont
                stockés hors de la racine web publique et ne sont accessibles qu’aux personnes autorisées.
            </p>
        </>
    );
}
