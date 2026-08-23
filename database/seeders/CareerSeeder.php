<?php

namespace Database\Seeders;

use App\Models\JobOpening;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        $openings = [
            [
                'slug' => 'conducteur-de-travaux-batiment',
                'title_fr' => 'Conducteur de travaux bâtiment',
                'title_en' => 'Building site manager',
                'department' => 'Opérations',
                'location' => 'Douala, Cameroun',
                'contract_type' => 'CDI',
                'experience_level' => '5 ans minimum',
                'salary_range' => '750 000 à 1 100 000 FCFA brut / mois',
                'position' => 1,
                'closes_at' => now()->addMonths(2)->toDateString(),
                'description_fr' => "<p>Vous prenez la responsabilité complète d'une opération de bâtiment, de la préparation de chantier à la levée des réserves. Vous encadrez les chefs de chantier, pilotez les sous-traitants et êtes l'interlocuteur direct du maître d'ouvrage.</p><p>Le poste est basé à Douala, avec des déplacements ponctuels sur nos opérations de Kribi et Yaoundé. Vous rapportez au Directeur des Opérations et gérez en moyenne deux chantiers simultanés.</p>",
                'description_en' => '<p>You take full responsibility for a building project, from site setup through to snag clearance. You supervise the foremen, manage subcontractors and act as the direct point of contact for the client.</p><p>The role is based in Douala, with occasional travel to our Kribi and Yaounde projects. You report to the Operations Director and typically run two concurrent sites.</p>',
                'requirements_fr' => [
                    'Diplôme d\'ingénieur génie civil ou BTS bâtiment avec expérience équivalente',
                    'Cinq ans minimum en conduite de travaux, dont deux sur des opérations supérieures à 500 M FCFA',
                    'Maîtrise de la lecture de plans et des notes de calcul',
                    'Pratique d\'un logiciel de planning (MS Project ou équivalent)',
                    'Permis B et disponibilité pour des déplacements régionaux',
                ],
                'requirements_en' => [
                    'Civil engineering degree or building technician diploma with equivalent experience',
                    'Minimum five years in site management, including two on projects above 500M XAF',
                    'Confident reading drawings and structural calculations',
                    'Working knowledge of a planning tool (MS Project or equivalent)',
                    'Driving licence and availability for regional travel',
                ],
                'benefits_fr' => [
                    'Véhicule de service et carburant',
                    'Assurance santé familiale',
                    'Prime de performance semestrielle indexée sur la marge du chantier',
                    'Plan de formation continue et prise en charge des certifications',
                ],
                'benefits_en' => [
                    'Company vehicle and fuel',
                    'Family health insurance',
                    'Half-yearly performance bonus linked to project margin',
                    'Continuous training plan with certification costs covered',
                ],
            ],
            [
                'slug' => 'ingenieur-structure',
                'title_fr' => 'Ingénieur structure',
                'title_en' => 'Structural engineer',
                'department' => 'Bureau d\'études',
                'location' => 'Douala, Cameroun',
                'contract_type' => 'CDI',
                'experience_level' => '3 ans minimum',
                'salary_range' => '620 000 à 900 000 FCFA brut / mois',
                'position' => 2,
                'closes_at' => now()->addMonths(3)->toDateString(),
                'description_fr' => "<p>Au sein d'une équipe de neuf personnes, vous produisez les notes de calcul et les plans d'exécution de nos opérations de bâtiment. Vous participez aux choix de conception en amont, notamment sur les systèmes de fondation en sol difficile.</p><p>Vous travaillez sur Robot Structural Analysis et Revit, dans un flux BIM que l'équipe a intégralement adopté depuis 2021.</p>",
                'description_en' => '<p>Within a team of nine, you produce the structural calculations and construction drawings for our building projects. You contribute to design decisions upstream, particularly on foundation systems in difficult ground.</p><p>You work on Robot Structural Analysis and Revit, in a BIM workflow the team fully adopted in 2021.</p>',
                'requirements_fr' => [
                    'Diplôme d\'ingénieur en génie civil, option structures',
                    'Trois ans minimum en bureau d\'études structure',
                    'Maîtrise des Eurocodes 2 et 8',
                    'Pratique de Robot Structural Analysis ou équivalent',
                    'Inscription à l\'ONIGC ou éligibilité à l\'inscription',
                ],
                'requirements_en' => [
                    'Civil engineering degree with a structures specialisation',
                    'Minimum three years in a structural design office',
                    'Confident with Eurocodes 2 and 8',
                    'Practical experience with Robot Structural Analysis or equivalent',
                    'ONIGC registration or eligibility to register',
                ],
                'benefits_fr' => [
                    'Assurance santé familiale',
                    'Prise en charge de l\'inscription à l\'Ordre',
                    'Deux jours de télétravail par semaine',
                    'Budget formation annuel individualisé',
                ],
                'benefits_en' => [
                    'Family health insurance',
                    'Order registration fees covered',
                    'Two days of remote work a week',
                    'Individual annual training budget',
                ],
            ],
            [
                'slug' => 'chef-de-chantier-gros-oeuvre',
                'title_fr' => 'Chef de chantier gros oeuvre',
                'title_en' => 'Structural works foreman',
                'department' => 'Opérations',
                'location' => 'Kribi, Cameroun',
                'contract_type' => 'CDD 24 mois',
                'experience_level' => '4 ans minimum',
                'salary_range' => '480 000 à 650 000 FCFA brut / mois',
                'position' => 3,
                'closes_at' => now()->addMonths(1)->toDateString(),
                'description_fr' => "<p>Poste basé sur notre chantier de l'hôtel Krystal Bay à Kribi, pour la durée de l'opération. Vous encadrez les équipes de coffrage, ferraillage et maçonnerie, organisez les approvisionnements quotidiens et faites respecter le plan de sécurité.</p><p>Logement de fonction fourni sur place.</p>",
                'description_en' => '<p>Based on our Krystal Bay hotel project in Kribi, for the duration of the works. You supervise the formwork, steel fixing and masonry teams, organise daily material deliveries and enforce the safety plan.</p><p>Accommodation provided on site.</p>',
                'requirements_fr' => [
                    'Formation technique bâtiment ou parcours de compagnonnage équivalent',
                    'Quatre ans minimum comme chef d\'équipe ou chef de chantier',
                    'Expérience du béton armé en site côtier appréciée',
                    'Capacité à encadrer une équipe de 25 à 40 personnes',
                    'Mobilité géographique sur la durée du chantier',
                ],
                'requirements_en' => [
                    'Technical building qualification or equivalent trade background',
                    'Minimum four years as team leader or foreman',
                    'Coastal reinforced concrete experience an advantage',
                    'Able to supervise a team of 25 to 40 people',
                    'Geographic mobility for the duration of the project',
                ],
                'benefits_fr' => [
                    'Logement de fonction à Kribi',
                    'Prime de déplacement mensuelle',
                    'Assurance santé familiale',
                    'Possibilité de CDI à l\'issue du chantier',
                ],
                'benefits_en' => [
                    'Company accommodation in Kribi',
                    'Monthly travel allowance',
                    'Family health insurance',
                    'Possible permanent contract at the end of the project',
                ],
            ],
            [
                'slug' => 'technicien-qhse',
                'title_fr' => 'Technicien QHSE',
                'title_en' => 'QHSE officer',
                'department' => 'QHSE',
                'location' => 'Douala, Cameroun',
                'contract_type' => 'CDI',
                'experience_level' => '2 ans minimum',
                'salary_range' => '420 000 à 580 000 FCFA brut / mois',
                'position' => 4,
                'description_fr' => "<p>Vous appuyez la Responsable QHSE dans l'audit de nos chantiers, l'animation des quarts d'heure sécurité et la tenue du système documentaire ISO 9001 et ISO 45001.</p><p>Le poste implique une présence terrain d'environ trois jours par semaine, réparties sur nos opérations en cours.</p>",
                'description_en' => '<p>You support the QHSE Manager in auditing our sites, running safety briefings and maintaining the ISO 9001 and ISO 45001 documentation system.</p><p>The role involves around three days a week on site, spread across our live projects.</p>',
                'requirements_fr' => [
                    'Formation HSE, QSE ou équivalent',
                    'Deux ans minimum en environnement BTP ou industriel',
                    'Connaissance des référentiels ISO 9001 et ISO 45001',
                    'Aisance rédactionnelle et rigueur documentaire',
                    'Permis B',
                ],
                'requirements_en' => [
                    'HSE, QSE or equivalent qualification',
                    'Minimum two years in construction or industry',
                    'Familiar with the ISO 9001 and ISO 45001 standards',
                    'Strong writing skills and documentary discipline',
                    'Driving licence',
                ],
                'benefits_fr' => [
                    'Assurance santé familiale',
                    'Formation certifiante prise en charge la première année',
                    'Véhicule de service partagé',
                ],
                'benefits_en' => [
                    'Family health insurance',
                    'Certification training funded in the first year',
                    'Shared company vehicle',
                ],
            ],
            [
                'slug' => 'apprenti-coffreur-ferrailleur',
                'title_fr' => 'Apprenti coffreur-ferrailleur',
                'title_en' => 'Formwork and steel fixing apprentice',
                'department' => 'Opérations',
                'location' => 'Douala, Cameroun',
                'contract_type' => 'Contrat d\'apprentissage',
                'experience_level' => 'Débutant accepté',
                'salary_range' => 'Selon la grille apprentissage',
                'position' => 5,
                'description_fr' => "<p>Formation en alternance de dix-huit mois sur nos chantiers de Douala, en partenariat avec les centres de formation professionnelle de la ville. Vous êtes rattaché à un compagnon référent qui n'encadre jamais plus de deux apprentis.</p><p>Nous recrutons entre dix-huit et vingt-quatre apprentis chaque année, et 61 % d'entre eux sont embauchés en CDI à l'issue de leur formation.</p>",
                'description_en' => '<p>An eighteen-month work-study programme on our Douala sites, in partnership with the city vocational training centres. You are attached to a mentor tradesperson who never supervises more than two apprentices.</p><p>We take on between eighteen and twenty-four apprentices each year, and 61% of them are hired on permanent contracts at the end of the programme.</p>',
                'requirements_fr' => [
                    'Âge compris entre 18 et 26 ans',
                    'Niveau CAP, BEP ou équivalent, ou motivation démontrée',
                    'Aptitude au travail physique en extérieur',
                    'Assiduité et respect strict des consignes de sécurité',
                ],
                'requirements_en' => [
                    'Aged between 18 and 26',
                    'Vocational certificate level or demonstrated motivation',
                    'Fit for physical outdoor work',
                    'Reliability and strict compliance with safety instructions',
                ],
                'benefits_fr' => [
                    'Équipements de protection individuelle fournis',
                    'Repas de midi pris en charge sur le chantier',
                    'Accompagnement par un compagnon référent',
                    'Perspective d\'embauche en CDI',
                ],
                'benefits_en' => [
                    'Personal protective equipment provided',
                    'Lunch covered on site',
                    'Support from a mentor tradesperson',
                    'Prospect of a permanent contract',
                ],
            ],
        ];

        foreach ($openings as $opening) {
            JobOpening::updateOrCreate(['slug' => $opening['slug']], $opening);
        }
    }
}
