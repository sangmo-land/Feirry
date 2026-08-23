<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Partner;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $this->members();
        $this->testimonials();
        $this->partners();
        $this->certifications();
    }

    protected function members(): void
    {
        $members = [
            [
                'slug' => 'serge-ndongo-mbappe',
                'name' => 'Serge Ndongo Mbappé',
                'role_fr' => 'Directeur Général & cofondateur',
                'role_en' => 'Managing Director & co-founder',
                'years_experience' => 24,
                'is_leadership' => true,
                'position' => 1,
                'email' => 'direction@feirry.org',
                'expertise' => ['Structures béton armé', 'Contractant général', 'Stratégie'],
                'bio_fr' => "Ingénieur en génie civil formé à l'École Nationale Supérieure Polytechnique de Yaoundé, puis huit ans en bureau d'études à Lyon avant de rentrer fonder FEIRRY en 2009. Il pilote personnellement les projets en contractant général de plus d'un milliard de francs.",
                'bio_en' => 'A civil engineer trained at the National Advanced School of Engineering in Yaounde, followed by eight years in a design office in Lyon before returning to found FEIRRY in 2009. He personally leads general contracting projects above one billion francs.',
            ],
            [
                'slug' => 'aicha-njoya',
                'name' => 'Aïcha Njoya',
                'role_fr' => 'Directrice Technique',
                'role_en' => 'Technical Director',
                'years_experience' => 18,
                'is_leadership' => true,
                'position' => 2,
                'email' => 'technique@feirry.org',
                'expertise' => ['Calcul de structures', 'Eurocodes', 'BIM'],
                'bio_fr' => "Elle dirige le bureau d'études interne et signe les notes de calcul de l'ensemble de nos ouvrages. Spécialiste des fondations en sol difficile, elle a introduit la modélisation BIM dans l'entreprise en 2019.",
                'bio_en' => 'She leads the in-house engineering office and signs off the structural calculations for all our projects. A specialist in foundations on difficult ground, she introduced BIM modelling to the company in 2019.',
            ],
            [
                'slug' => 'emmanuel-tchoumi',
                'name' => 'Emmanuel Tchoumi',
                'role_fr' => 'Directeur des Opérations',
                'role_en' => 'Operations Director',
                'years_experience' => 21,
                'is_leadership' => true,
                'position' => 3,
                'expertise' => ['Conduite de travaux', 'Planning', 'Achats'],
                'bio_fr' => "Vingt et un ans de chantier, dont onze chez FEIRRY. Il supervise l'ensemble des conducteurs de travaux et arbitre les plannings entre les opérations simultanées. Passé par le compagnonnage avant l'école d'ingénieurs, il connaît les métiers de l'intérieur.",
                'bio_en' => 'Twenty-one years on site, eleven of them at FEIRRY. He supervises all site managers and arbitrates programmes across concurrent operations. Having come up through the trades before engineering school, he knows the crafts from the inside.',
            ],
            [
                'slug' => 'clarisse-ekwalla',
                'name' => 'Clarisse Ekwalla',
                'role_fr' => 'Directrice Administrative & Financière',
                'role_en' => 'Finance & Administration Director',
                'years_experience' => 16,
                'is_leadership' => true,
                'position' => 4,
                'expertise' => ['Contrôle de gestion', 'Financement de projet', 'Fiscalité OHADA'],
                'bio_fr' => "Expert-comptable diplômée, elle a mis en place le contrôle de gestion par affaire qui permet aujourd'hui de suivre la marge de chaque chantier au réel, semaine par semaine.",
                'bio_en' => 'A qualified chartered accountant, she built the project-level management accounting system that now tracks the actual margin on each site, week by week.',
            ],
            [
                'slug' => 'patrick-fokou',
                'name' => 'Patrick Fokou',
                'role_fr' => "Responsable Bureau d'Études",
                'role_en' => 'Head of Engineering Office',
                'years_experience' => 13,
                'position' => 5,
                'expertise' => ['Robot Structural Analysis', 'Revit', 'Métrés'],
                'bio_fr' => "Il encadre l'équipe de projeteurs et produit les plans d'exécution. Formateur interne sur Revit, il a fait passer l'ensemble du bureau d'études au flux BIM en dix-huit mois.",
                'bio_en' => 'He leads the drafting team and produces the construction drawings. An internal Revit trainer, he moved the whole engineering office onto a BIM workflow in eighteen months.',
            ],
            [
                'slug' => 'marthe-bilongo',
                'name' => 'Marthe Bilongo',
                'role_fr' => 'Responsable QHSE',
                'role_en' => 'QHSE Manager',
                'years_experience' => 12,
                'position' => 6,
                'expertise' => ['ISO 45001', 'Plans de prévention', 'Formation sécurité'],
                'bio_fr' => "Elle audite chaque chantier deux fois par mois et anime les quarts d'heure sécurité. Le passage de l'entreprise à zéro accident grave depuis 2019 lui doit beaucoup.",
                'bio_en' => 'She audits every site twice a month and runs the safety briefings. The company record of zero serious accidents since 2019 owes a great deal to her.',
            ],
            [
                'slug' => 'rodrigue-manga',
                'name' => 'Rodrigue Manga',
                'role_fr' => 'Conducteur de travaux principal',
                'role_en' => 'Senior Site Manager',
                'years_experience' => 15,
                'position' => 7,
                'expertise' => ['Gros oeuvre', 'Coordination de lots', 'Site occupé'],
                'bio_fr' => "Spécialiste des chantiers en site contraint et en site occupé. Il a conduit la réhabilitation d'Akwa et l'extension de Bonabéri sans interruption d'exploitation.",
                'bio_en' => 'A specialist in constrained and occupied sites. He ran the Akwa refurbishment and the Bonaberi extension with no interruption to trading.',
            ],
            [
                'slug' => 'jean-paul-ateba',
                'name' => 'Jean-Paul Ateba',
                'role_fr' => 'Responsable du parc matériel',
                'role_en' => 'Plant Manager',
                'years_experience' => 19,
                'position' => 8,
                'expertise' => ['Engins de chantier', 'Maintenance préventive', 'Logistique'],
                'bio_fr' => "Il gère les 42 engins et véhicules de l'entreprise depuis notre base de Bonabéri, avec un atelier de maintenance intégré. Le taux de disponibilité du parc dépasse 94 %.",
                'bio_en' => 'He manages the 42 machines and vehicles from our Bonaberi depot, with an in-house maintenance workshop. Fleet availability runs above 94%.',
            ],
            [
                'slug' => 'nadege-sop',
                'name' => 'Nadège Sop',
                'role_fr' => 'Responsable Ressources Humaines',
                'role_en' => 'HR Manager',
                'years_experience' => 10,
                'position' => 9,
                'email' => 'recrutement@feirry.org',
                'expertise' => ['Recrutement technique', 'Formation', 'Droit du travail'],
                'bio_fr' => "Elle pilote le recrutement et le plan de formation, dont l'apprentissage interne qui forme chaque année une vingtaine de jeunes compagnons sur nos chantiers.",
                'bio_en' => 'She leads recruitment and the training plan, including the in-house apprenticeship that trains around twenty young tradespeople on our sites each year.',
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(['slug' => $member['slug']], $member);
        }
    }

    protected function testimonials(): void
    {
        $projects = Project::pluck('id', 'slug');

        $testimonials = [
            [
                'author_name' => 'Marcel Ebongue',
                'author_role_fr' => 'Directeur Général',
                'author_role_en' => 'Managing Director',
                'company' => 'Groupe MAFTECH SA',
                'project_id' => $projects['siege-maftech-bonanjo'] ?? null,
                'rating' => 5,
                'is_featured' => true,
                'position' => 1,
                'content_fr' => "Nous avons emménagé à la date prévue au contrat, ce qui, franchement, n'était pas ce que notre expérience du secteur nous laissait espérer. Les comptes de chantier nous étaient ouverts chaque mois, et les deux avenants signés en vingt-six mois portaient sur des demandes qui venaient de nous.",
                'content_en' => 'We moved in on the contractual date, which frankly is not what our experience of the sector had led us to expect. The site accounts were open to us every month, and the two variations signed over twenty-six months both came from changes we requested.',
            ],
            [
                'author_name' => 'Dr Sylvie Manga',
                'author_role_fr' => 'Directrice médicale',
                'author_role_en' => 'Medical Director',
                'company' => 'Groupe Santé Wouri',
                'project_id' => $projects['centre-medical-deido'] ?? null,
                'rating' => 5,
                'is_featured' => true,
                'position' => 2,
                'content_fr' => "Obtenir l'agrément d'exploitation sans réserve à la première visite est rare. Cela veut dire que les contraintes réglementaires avaient été intégrées dès la conception, et pas corrigées à la fin. C'est exactement ce qu'un exploitant de santé attend d'un constructeur.",
                'content_en' => 'Getting the operating licence with no reservations at the first inspection is rare. It means the regulatory constraints were built into the design, not corrected at the end. That is exactly what a healthcare operator needs from a contractor.',
            ],
            [
                'author_name' => 'Thierry Kamdem',
                'author_role_fr' => 'Investisseur particulier',
                'author_role_en' => 'Private client',
                'company' => 'Bruxelles, Belgique',
                'project_id' => $projects['villa-contemporaine-bonamoussadi'] ?? null,
                'rating' => 5,
                'is_featured' => true,
                'position' => 3,
                'content_fr' => "J'avais déjà perdu de l'argent sur un chantier abandonné. Cette fois j'ai reçu un rapport photo daté tous les vendredis pendant treize mois, et toutes les factures fournisseurs en copie. Je leur ai confié deux autres villas depuis.",
                'content_en' => 'I had already lost money on an abandoned project. This time I received a dated photo report every Friday for thirteen months, and every supplier invoice in copy. I have since commissioned two more villas from them.',
            ],
            [
                'author_name' => 'Père Antoine Bekolo',
                'author_role_fr' => 'Président du conseil',
                'author_role_en' => 'Chair of the board',
                'company' => 'Fondation Éducative du Littoral',
                'project_id' => $projects['complexe-scolaire-makepe'] ?? null,
                'rating' => 5,
                'is_featured' => true,
                'position' => 4,
                'content_fr' => "Ils ont livré une semaine avant la rentrée, et ils nous ont fait économiser le poste climatisation en concevant des salles réellement ventilées. Cet argent est devenu un laboratoire de sciences. Peu d'entreprises proposent d'elles-mêmes de réduire leur propre marché.",
                'content_en' => 'They delivered a week before term started, and they saved us the air conditioning package by designing classrooms that genuinely ventilate. That money became a science laboratory. Few contractors volunteer to reduce their own scope.',
            ],
            [
                'author_name' => 'Estelle Nguema',
                'author_role_fr' => 'Directrice des opérations',
                'author_role_en' => 'Operations Director',
                'company' => 'Société Camerounaise de Distribution',
                'project_id' => $projects['plateforme-logistique-bassa'] ?? null,
                'rating' => 5,
                'is_featured' => true,
                'position' => 5,
                'content_fr' => "Dix-huit mois d'exploitation intensive, aucune fissure sur le dallage. Quand on connaît la qualité des remblais de Bassa, ce n'est pas de la chance : c'est le traitement de sol qu'ils nous avaient recommandé et que nous avions failli refuser pour économiser.",
                'content_en' => 'Eighteen months of intensive operation, not a crack in the slab. Anyone who knows the quality of the fill in Bassa knows that is not luck: it is the ground treatment they recommended and that we very nearly turned down to save money.',
            ],
            [
                'author_name' => 'Georges Njike',
                'author_role_fr' => 'Gérant',
                'author_role_en' => 'Managing partner',
                'company' => 'SCI Boulevard de la Liberté',
                'project_id' => $projects['rehabilitation-immeuble-akwa'] ?? null,
                'rating' => 5,
                'is_featured' => true,
                'position' => 6,
                'content_fr' => "Seize mois de travaux, sept locataires en place, aucune résiliation. Ils ont négocié les plages horaires bruyantes locataire par locataire avant de commencer. C'est ce niveau de préparation qui fait la différence sur un site occupé.",
                'content_en' => 'Sixteen months of work, seven sitting tenants, not one termination. They negotiated the noisy work windows tenant by tenant before starting. That level of preparation is what makes the difference on an occupied site.',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(
                ['author_name' => $testimonial['author_name'], 'company' => $testimonial['company']],
                $testimonial,
            );
        }
    }

    protected function partners(): void
    {
        $partners = [
            ['name' => 'Groupe MAFTECH', 'type' => 'client', 'position' => 1],
            ['name' => 'Cameroon Food Industries', 'type' => 'client', 'position' => 2],
            ['name' => 'Société Camerounaise de Distribution', 'type' => 'client', 'position' => 3],
            ['name' => 'Krystal Hospitality', 'type' => 'client', 'position' => 4],
            ['name' => 'Groupe Santé Wouri', 'type' => 'client', 'position' => 5],
            ['name' => 'Fondation Éducative du Littoral', 'type' => 'client', 'position' => 6],
            ['name' => 'Société Immobilière du Cameroun', 'type' => 'client', 'position' => 7],
            ['name' => 'Wouri Retail Development', 'type' => 'client', 'position' => 8],
            ['name' => 'CIMENCAM', 'type' => 'supplier', 'position' => 9],
            ['name' => 'Atelier Wouri Architectes', 'type' => 'partner', 'position' => 10],
        ];

        foreach ($partners as $partner) {
            Partner::updateOrCreate(['name' => $partner['name']], $partner);
        }
    }

    protected function certifications(): void
    {
        $certifications = [
            [
                'name' => 'ISO 9001:2015',
                'issuer' => 'Bureau Veritas Certification',
                'year' => 2020,
                'position' => 1,
                'description_fr' => "Système de management de la qualité certifié pour les activités de conception et de réalisation d'ouvrages de bâtiment et de génie civil.",
                'description_en' => 'Certified quality management system covering the design and construction of building and civil engineering works.',
            ],
            [
                'name' => 'ISO 45001:2018',
                'issuer' => 'Bureau Veritas Certification',
                'year' => 2022,
                'position' => 2,
                'description_fr' => "Management de la santé et de la sécurité au travail, appliqué à l'ensemble de nos chantiers et de notre base logistique.",
                'description_en' => 'Occupational health and safety management, applied across all our sites and our logistics base.',
            ],
            [
                'name' => 'Agrément MINTP catégorie B',
                'issuer' => 'Ministère des Travaux Publics',
                'year' => 2015,
                'position' => 3,
                'description_fr' => "Agrément permettant de soumissionner aux marchés publics de bâtiment et de génie civil sans limitation de montant en catégorie B.",
                'description_en' => 'Approval to bid for public building and civil engineering contracts without value limitation in category B.',
            ],
            [
                'name' => 'ONIGC',
                'issuer' => 'Ordre National des Ingénieurs de Génie Civil du Cameroun',
                'year' => 2011,
                'position' => 4,
                'description_fr' => "Nos ingénieurs structure sont inscrits à l'Ordre, condition de la signature des notes de calcul déposées en permis de construire.",
                'description_en' => 'Our structural engineers are registered with the Order, a requirement for signing calculations submitted with building permits.',
            ],
            [
                'name' => 'Garantie décennale',
                'issuer' => 'Chanas Assurances',
                'year' => 2009,
                'position' => 5,
                'description_fr' => "Police de responsabilité civile décennale couvrant l'intégralité de nos ouvrages, attestation remise à chaque maître d'ouvrage à la réception.",
                'description_en' => 'Ten-year structural liability cover on all our works, with the certificate issued to every client at handover.',
            ],
        ];

        foreach ($certifications as $certification) {
            Certification::updateOrCreate(['name' => $certification['name']], $certification);
        }
    }
}
