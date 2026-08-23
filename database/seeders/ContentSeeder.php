<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Milestone;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\ProcessStep;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->processSteps();
        $this->milestones();
        $this->posts();
        $this->faqs();
    }

    protected function processSteps(): void
    {
        $steps = [
            [
                'position' => 1,
                'icon' => 'message-square',
                'title_fr' => 'Écoute et faisabilité',
                'title_en' => 'Listening and feasibility',
                'duration_fr' => '1 à 2 semaines',
                'duration_en' => '1 to 2 weeks',
                'description_fr' => "Un premier rendez-vous, puis une visite du terrain. Nous vérifions ce que le site autorise réellement : nature du sol, accès des engins, servitudes, règles d'urbanisme du secteur. Beaucoup de projets changent de forme à ce stade, et c'est le moment le moins cher pour le faire.",
                'description_en' => 'A first meeting, then a site visit. We check what the site actually allows: soil conditions, plant access, easements, local planning rules. Many projects change shape at this stage, and this is the cheapest moment for that to happen.',
                'deliverables_fr' => ['Compte rendu de visite technique', 'Note de faisabilité', 'Fourchette budgétaire indicative'],
                'deliverables_en' => ['Technical visit report', 'Feasibility note', 'Indicative budget range'],
            ],
            [
                'position' => 2,
                'icon' => 'ruler',
                'title_fr' => 'Études et chiffrage',
                'title_en' => 'Design and pricing',
                'duration_fr' => '3 à 8 semaines',
                'duration_en' => '3 to 8 weeks',
                'description_fr' => "Notre bureau d'études produit les plans, les notes de calcul et le métré. Le devis qui en sort est descriptif : chaque poste est décrit, quantifié et daté. Vous savez ce que vous achetez, ligne par ligne, avant de signer quoi que ce soit.",
                'description_en' => 'Our engineering office produces the drawings, calculations and quantities. The resulting quote is descriptive: every item is described, quantified and dated. You know what you are buying, line by line, before signing anything.',
                'deliverables_fr' => ['Plans d\'avant-projet', 'Notes de calcul structure', 'Devis descriptif quantitatif', 'Planning prévisionnel'],
                'deliverables_en' => ['Scheme design drawings', 'Structural calculations', 'Priced bill of quantities', 'Outline programme'],
            ],
            [
                'position' => 3,
                'icon' => 'clipboard-check',
                'title_fr' => 'Préparation de chantier',
                'title_en' => 'Site preparation',
                'duration_fr' => '2 à 4 semaines',
                'duration_en' => '2 to 4 weeks',
                'description_fr' => "Dossier de permis, installation de chantier, plan de sécurité, commandes des approvisionnements longs. C'est la phase invisible, celle que les chantiers en retard ont généralement bâclée.",
                'description_en' => 'Permit file, site setup, safety plan, orders placed for long-lead materials. This is the invisible phase, and the one that late projects have usually rushed.',
                'deliverables_fr' => ['Permis de construire déposé', 'Plan d\'installation de chantier', 'Plan particulier de sécurité', 'Planning contractuel détaillé'],
                'deliverables_en' => ['Building permit submitted', 'Site layout plan', 'Site-specific safety plan', 'Detailed contractual programme'],
            ],
            [
                'position' => 4,
                'icon' => 'hard-hat',
                'title_fr' => 'Exécution et suivi',
                'title_en' => 'Construction and monitoring',
                'duration_fr' => 'Selon le programme',
                'duration_en' => 'Per the programme',
                'description_fr' => "Les travaux démarrent. Un point hebdomadaire, un rapport photo daté, et un tableau d'avancement comparé au planning contractuel. Les essais béton, les contrôles de ferraillage et les réceptions de supports sont documentés au fil de l'eau, pas reconstitués à la fin.",
                'description_en' => 'Works start. A weekly review, a dated photo report, and a progress table compared against the contractual programme. Concrete tests, reinforcement checks and substrate approvals are documented as they happen, not reconstructed at the end.',
                'deliverables_fr' => ['Comptes rendus de chantier hebdomadaires', 'Rapports photographiques datés', 'Procès-verbaux d\'essais', 'Situation de travaux mensuelle'],
                'deliverables_en' => ['Weekly site minutes', 'Dated photo reports', 'Test certificates', 'Monthly valuation'],
            ],
            [
                'position' => 5,
                'icon' => 'key-round',
                'title_fr' => 'Réception et garanties',
                'title_en' => 'Handover and warranties',
                'duration_fr' => '2 à 6 semaines',
                'duration_en' => '2 to 6 weeks',
                'description_fr' => "Pré-réception avec liste de réserves, levée des réserves, puis réception définitive. Nous remettons le dossier des ouvrages exécutés complet et l'attestation de garantie décennale. La garantie de parfait achèvement court ensuite pendant un an.",
                'description_en' => 'Pre-handover inspection with a snag list, snags cleared, then final acceptance. We hand over the complete as-built file and the ten-year warranty certificate. The defects liability period then runs for one year.',
                'deliverables_fr' => ['Procès-verbal de réception', 'Dossier des ouvrages exécutés', 'Attestation de garantie décennale', 'Notices d\'entretien des équipements'],
                'deliverables_en' => ['Handover certificate', 'As-built documentation', 'Ten-year warranty certificate', 'Equipment maintenance manuals'],
            ],
        ];

        foreach ($steps as $step) {
            ProcessStep::updateOrCreate(['position' => $step['position']], $step);
        }
    }

    protected function milestones(): void
    {
        $milestones = [
            [
                'year' => 2009, 'position' => 1,
                'title_fr' => 'Trois ingénieurs, un bureau à Akwa',
                'title_en' => 'Three engineers, one office in Akwa',
                'description_fr' => "FEIRRY est immatriculée au registre du commerce de Douala avec un capital de 5 millions de francs et trois associés. Le premier chantier est une villa de 180 m² à Bonapriso.",
                'description_en' => 'FEIRRY is entered in the Douala trade register with capital of 5 million francs and three partners. The first project is a 180 m² villa in Bonapriso.',
            ],
            [
                'year' => 2012, 'position' => 2,
                'title_fr' => 'Premier immeuble R+5',
                'title_en' => 'First five-storey building',
                'description_fr' => "Livraison d'un immeuble de logements à Bonamoussadi, premier projet de l'entreprise dépassant 100 millions de francs. L'effectif atteint 45 personnes.",
                'description_en' => 'Delivery of an apartment building in Bonamoussadi, the first company project above 100 million francs. Headcount reaches 45.',
            ],
            [
                'year' => 2015, 'position' => 3,
                'title_fr' => 'Agrément MINTP catégorie B',
                'title_en' => 'MINTP category B approval',
                'description_fr' => "L'agrément du Ministère des Travaux Publics ouvre l'accès aux marchés publics. Le bureau d'études interne est créé la même année avec trois ingénieurs.",
                'description_en' => 'Approval from the Ministry of Public Works opens access to public contracts. The in-house engineering office is created the same year with three engineers.',
            ],
            [
                'year' => 2018, 'position' => 4,
                'title_fr' => 'Centième chantier livré',
                'title_en' => 'One hundredth project delivered',
                'description_fr' => "Le centième ouvrage est réceptionné, un entrepôt de 3 200 m² à Bassa. L'entreprise acquiert sa base logistique de Bonabéri et son premier parc d'engins en propre.",
                'description_en' => 'The hundredth project is handed over, a 3,200 m² warehouse in Bassa. The company acquires its Bonaberi logistics base and its first owned plant fleet.',
            ],
            [
                'year' => 2020, 'position' => 5,
                'title_fr' => 'Certification ISO 9001',
                'title_en' => 'ISO 9001 certification',
                'description_fr' => "Le système qualité est certifié par Bureau Veritas. La même année, aucun accident grave n'est déclaré pour la deuxième année consécutive.",
                'description_en' => 'The quality system is certified by Bureau Veritas. The same year, no serious accident is recorded for the second consecutive year.',
            ],
            [
                'year' => 2022, 'position' => 6,
                'title_fr' => 'Ouverture de la base de Yaoundé',
                'title_en' => 'Yaounde base opens',
                'description_fr' => "Le chantier de l'usine de Nsimalen conduit à l'ouverture d'une base permanente dans la capitale, qui sert désormais les régions du Centre et du Sud.",
                'description_en' => 'The Nsimalen plant project leads to a permanent base in the capital, now serving the Centre and South regions.',
            ],
            [
                'year' => 2025, 'position' => 7,
                'title_fr' => '385 collaborateurs, 240 ouvrages',
                'title_en' => '385 people, 240 projects',
                'description_fr' => "L'entreprise emploie 385 personnes en direct, dispose de 42 engins et a livré 240 ouvrages depuis sa création. Kribi devient sa troisième implantation.",
                'description_en' => 'The company directly employs 385 people, runs 42 machines and has delivered 240 projects since inception. Kribi becomes its third location.',
            ],
        ];

        foreach ($milestones as $milestone) {
            Milestone::updateOrCreate(['year' => $milestone['year']], $milestone);
        }
    }

    protected function posts(): void
    {
        $categories = [
            ['slug' => 'chantiers', 'name_fr' => 'Chantiers', 'name_en' => 'Site reports', 'color' => '#f2a33c', 'position' => 1],
            ['slug' => 'reglementation', 'name_fr' => 'Réglementation', 'name_en' => 'Regulation', 'color' => '#0f6b45', 'position' => 2],
            ['slug' => 'techniques-materiaux', 'name_fr' => 'Techniques & matériaux', 'name_en' => 'Techniques & materials', 'color' => '#253140', 'position' => 3],
            ['slug' => 'vie-entreprise', 'name_fr' => "Vie de l'entreprise", 'name_en' => 'Company news', 'color' => '#c2410c', 'position' => 4],
        ];

        foreach ($categories as $category) {
            PostCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }

        $ids = PostCategory::pluck('id', 'slug');
        $author = User::first();

        $posts = [
            [
                'slug' => 'permis-de-construire-douala-guide',
                'post_category_id' => $ids['reglementation'],
                'title_fr' => 'Permis de construire à Douala : ce que la mairie attend vraiment',
                'title_en' => 'Building permits in Douala: what the council actually expects',
                'reading_minutes' => 7,
                'is_featured' => true,
                'published_at' => now()->subDays(6),
                'views' => 1284,
                'tags' => ['permis de construire', 'urbanisme', 'Douala'],
                'excerpt_fr' => "Un dossier de permis rejeté fait perdre entre six et dix semaines. Dans la majorité des cas, le motif tient à trois pièces mal préparées. Voici lesquelles.",
                'excerpt_en' => 'A rejected permit application costs six to ten weeks. In most cases the reason comes down to three badly prepared documents. Here is which ones.',
                'cover_image' => 'blog/covers/permis-de-construire-douala-guide.jpg',
                'body_fr' => "<p>Sur les dossiers que nous déposons pour nos clients auprès des communes d'arrondissement de Douala, le délai d'instruction moyen tourne autour de huit semaines lorsque le dossier est complet. Quand il ne l'est pas, il faut compter le double, parfois davantage.</p><h2>1. Le titre foncier, et rien d'autre</h2><p>Une attestation de vente, un certificat de propriété coutumière ou une promesse de cession ne suffisent pas. Le service d'urbanisme demande le titre foncier ou, à défaut, le certificat de propriété délivré par la conservation foncière. Régulariser cette pièce prend souvent plus de temps que l'instruction du permis elle-même : il faut donc s'en occuper en premier, pas en dernier.</p><h2>2. Une note de calcul signée par un ingénieur inscrit à l'Ordre</h2><p>Pour tout bâtiment dépassant un rez-de-chaussée plus deux étages, la note de calcul de structure doit être signée par un ingénieur génie civil inscrit à l'ONIGC. Un plan de coffrage sans note de calcul associée constitue le deuxième motif de rejet que nous rencontrons.</p><h2>3. L'implantation cotée par rapport aux limites séparatives</h2><p>Le plan de masse doit indiquer les distances aux limites de propriété, les servitudes de passage et les reculs par rapport à la voie. Un plan de masse sans cotes est un plan de masse incomplet, même si l'architecture est irréprochable.</p><h2>Ce qui accélère réellement l'instruction</h2><p>Trois habitudes simples font gagner du temps : déposer un dossier en cinq exemplaires plutôt que les trois demandés, joindre une note descriptive d'une page qui explique le projet en langage clair, et identifier dès le dépôt un interlocuteur unique côté maître d'ouvrage. Les demandes de pièces complémentaires arrivent souvent par téléphone, et un dossier dont personne ne répond dort dans un bureau.</p>",
                'body_en' => '<p>On the applications we file for clients with the Douala district councils, the average processing time is around eight weeks when the file is complete. When it is not, expect double, sometimes more.</p><h2>1. The land title, and nothing else</h2><p>A sale attestation, a customary ownership certificate or a promise to sell will not do. The planning department asks for the land title or, failing that, the ownership certificate issued by the land registry. Regularising this document often takes longer than the permit process itself, so deal with it first, not last.</p><h2>2. Calculations signed by an engineer registered with the Order</h2><p>For any building above ground plus two floors, the structural calculations must be signed by a civil engineer registered with ONIGC. A formwork drawing with no associated calculation is the second most common ground for rejection we encounter.</p><h2>3. A dimensioned site plan relative to boundaries</h2><p>The site plan must show distances to property boundaries, rights of way and setbacks from the road. A site plan without dimensions is an incomplete site plan, however impeccable the architecture.</p><h2>What genuinely speeds things up</h2><p>Three simple habits save time: file five copies rather than the three requested, attach a one-page descriptive note explaining the project in plain language, and name a single client-side contact at the point of filing. Requests for additional documents often come by phone, and a file nobody answers for sits in a drawer.</p>',
            ],
            [
                'slug' => 'construire-saison-des-pluies-littoral',
                'post_category_id' => $ids['chantiers'],
                'title_fr' => 'Construire pendant la saison des pluies : ce qui se planifie et ce qui s\'arrête',
                'title_en' => 'Building through the rainy season: what can be planned and what stops',
                'reading_minutes' => 6,
                'published_at' => now()->subDays(19),
                'views' => 947,
                'tags' => ['planning', 'saison des pluies', 'organisation'],
                'excerpt_fr' => "De juin à octobre, le Littoral reçoit près de 70 % de sa pluviométrie annuelle. Arrêter le chantier n'est pas une option viable. Le réorganiser en est une.",
                'excerpt_en' => 'From June to October the Littoral receives nearly 70% of its annual rainfall. Stopping the site is not a viable option. Reorganising it is.',
                'cover_image' => 'blog/covers/construire-saison-des-pluies-littoral.jpg',
                'body_fr' => "<p>Douala reçoit en moyenne 4 000 millimètres de pluie par an, dont l'essentiel entre juin et octobre. Un planning qui ignore cette réalité est un planning qui dérapera de trois mois.</p><h2>Ce qui ne peut pas attendre la saison sèche</h2><p>Les terrassements en grande masse, les fouilles profondes et les remblais compactés sont les postes les plus vulnérables. Une fouille ouverte pendant un épisode pluvieux se transforme en bassin, et le fond de forme détrempé doit être purgé puis reconstitué : deux fois le travail, deux fois le coût.</p><p>Nous planifions donc systématiquement ces phases entre novembre et mai, quitte à décaler le démarrage d'un chantier de six semaines. Ce décalage se rattrape largement sur la suite.</p><h2>Ce qui peut se poursuivre sans perte</h2><p>Une fois le bâtiment hors d'eau, la pluie devient une contrainte d'organisation plutôt qu'un obstacle technique. Le second oeuvre, les réseaux intérieurs, les cloisons et les finitions se poursuivent normalement. Nous privilégions alors la mise hors d'eau rapide, quitte à traiter les façades plus tard.</p><h2>Les mesures qui font la différence</h2><ul><li>Drainage périphérique du chantier dès l'installation, jamais après</li><li>Stockage des ciments et des isolants sur palettes surélevées et bâchées</li><li>Pistes de circulation empierrées pour les engins, refaites tous les deux mois</li><li>Coulages programmés sur les créneaux météo, avec un bulletin consulté la veille</li></ul><p>Le surcoût de ces mesures représente environ 1,5 % du montant des travaux. Une semaine d'arrêt de chantier coûte davantage.</p>",
                'body_en' => '<p>Douala receives an average of 4,000 millimetres of rain a year, most of it between June and October. A programme that ignores this reality is a programme that will slip by three months.</p><h2>What cannot wait for the dry season</h2><p>Bulk earthworks, deep excavation and compacted fill are the most vulnerable activities. An open excavation during a rain event turns into a pond, and the saturated formation has to be dug out and rebuilt: twice the work, twice the cost.</p><p>So we systematically plan these phases between November and May, even if that delays a project start by six weeks. That delay is more than recovered later.</p><h2>What can continue without loss</h2><p>Once the building is watertight, rain becomes an organisational constraint rather than a technical obstacle. Fit-out, internal services, partitions and finishes carry on normally. We therefore prioritise getting watertight quickly, and deal with the facades later.</p><h2>The measures that make the difference</h2><ul><li>Perimeter site drainage from setup, never afterwards</li><li>Cement and insulation stored on raised, sheeted pallets</li><li>Stoned haul roads for plant, rebuilt every two months</li><li>Pours scheduled against the forecast, checked the day before</li></ul><p>The extra cost of these measures is around 1.5% of the works value. One week of site shutdown costs more.</p>',
            ],
            [
                'slug' => 'beton-cote-corrosion-saline',
                'post_category_id' => $ids['techniques-materiaux'],
                'title_fr' => 'Béton en bord de mer : pourquoi 50 mm d\'enrobage ne sont pas du luxe',
                'title_en' => 'Coastal concrete: why 50 mm of cover is not a luxury',
                'reading_minutes' => 8,
                'published_at' => now()->subDays(34),
                'views' => 712,
                'tags' => ['béton', 'corrosion', 'Kribi', 'durabilité'],
                'excerpt_fr' => "À Kribi comme à Bonabéri, les chlorures marins traversent un béton ordinaire en moins de dix ans. Le surcoût d'un béton adapté est de 6 %. Celui d'une reprise structurelle est de 300 %.",
                'excerpt_en' => 'In Kribi as in Bonaberi, marine chlorides penetrate ordinary concrete in under ten years. The extra cost of a suitable concrete is 6%. The cost of structural repair is 300%.',
                'cover_image' => 'blog/covers/beton-cote-corrosion-saline.jpg',
                'body_fr' => "<p>La corrosion des armatures est la première cause de dégradation structurelle des bâtiments côtiers camerounais. Le mécanisme est connu depuis longtemps, et il est pourtant systématiquement sous-estimé au moment du chiffrage.</p><h2>Comment les chlorures détruisent une structure</h2><p>Les embruns marins déposent du chlorure de sodium sur les façades. Ce sel migre à travers la porosité du béton jusqu'à l'acier. Quand la concentration atteint le seuil critique, la couche passive qui protège l'armature se rompt. L'acier rouille, gonfle de deux à sept fois son volume initial, et fait éclater le béton d'enrobage.</p><p>À partir de là, l'acier est directement exposé, et le processus s'accélère de manière exponentielle.</p><h2>Les trois leviers</h2><p><strong>L'enrobage.</strong> C'est l'épaisseur de béton entre la surface et l'armature. En classe d'exposition XS3, l'Eurocode 2 impose 50 mm minimum. Nous voyons régulièrement des ouvrages côtiers exécutés avec 25 ou 30 mm, valeur correcte en milieu urbain sec et gravement insuffisante en bord de mer.</p><p><strong>La compacité du béton.</strong> Un rapport eau/ciment inférieur à 0,45 réduit drastiquement la porosité. C'est moins une question de dosage en ciment que de discipline sur l'ajout d'eau au moment du coulage, pratique courante pour faciliter la mise en oeuvre et catastrophique pour la durabilité.</p><p><strong>Le ciment.</strong> Un CEM III ou un ciment avec addition de laitier offre une bien meilleure résistance à la pénétration des chlorures qu'un CEM I ordinaire.</p><h2>Le calcul économique</h2><p>Sur l'hôtel de Kribi, le passage à un béton adapté et à 50 mm d'enrobage a représenté environ 6 % du montant du lot gros oeuvre. Une reprise structurelle de façades corrodées, dix ans plus tard, coûte entre deux et trois fois le prix du gros oeuvre initial de ces façades, sans compter la perte d'exploitation.</p>",
                'body_en' => '<p>Reinforcement corrosion is the leading cause of structural degradation in Cameroonian coastal buildings. The mechanism has been understood for a long time, and it is still routinely underestimated at pricing stage.</p><h2>How chlorides destroy a structure</h2><p>Sea spray deposits sodium chloride on facades. That salt migrates through the porosity of the concrete to the steel. When the concentration reaches the critical threshold, the passive layer protecting the reinforcement breaks down. The steel rusts, expands to between two and seven times its original volume, and spalls off the cover concrete.</p><p>From that point the steel is directly exposed, and the process accelerates exponentially.</p><h2>The three levers</h2><p><strong>Cover.</strong> This is the thickness of concrete between the surface and the reinforcement. In exposure class XS3, Eurocode 2 requires a 50 mm minimum. We regularly see coastal structures built with 25 or 30 mm, a correct value in a dry urban environment and severely inadequate on the coast.</p><p><strong>Concrete density.</strong> A water/cement ratio below 0.45 drastically reduces porosity. This is less about cement content than about discipline on adding water at the point of pouring, a common practice to ease placement and a catastrophic one for durability.</p><p><strong>The cement.</strong> A CEM III or slag-blended cement offers far better resistance to chloride penetration than an ordinary CEM I.</p><h2>The economics</h2><p>On the Kribi hotel, moving to a suitable concrete and 50 mm cover represented around 6% of the structural package. Structural repair of corroded facades, ten years later, costs between two and three times the original structural cost of those facades, before counting lost trading.</p>',
            ],
            [
                'slug' => 'choisir-entreprise-construction-cameroun',
                'post_category_id' => $ids['reglementation'],
                'title_fr' => 'Choisir son constructeur : les six vérifications avant de signer',
                'title_en' => 'Choosing your contractor: six checks before you sign',
                'reading_minutes' => 6,
                'published_at' => now()->subDays(48),
                'views' => 1653,
                'tags' => ['conseils', 'contrat', 'garantie décennale'],
                'excerpt_fr' => "Le chantier abandonné reste le premier risque du particulier qui construit au Cameroun. Six vérifications de dix minutes chacune éliminent l'essentiel du risque.",
                'excerpt_en' => 'The abandoned project remains the biggest risk for private clients building in Cameroon. Six checks, ten minutes each, remove most of that risk.',
                'cover_image' => 'blog/covers/choisir-entreprise-construction-cameroun.jpg',
                'body_fr' => "<p>Nous reprenons chaque année deux à trois chantiers abandonnés par une autre entreprise. Le schéma est presque toujours le même : un devis nettement moins cher que les autres, des acomptes importants demandés tôt, et un arrêt de chantier au moment où le solde ne couvre plus les travaux restants.</p><h2>1. Le registre de commerce et le NIU</h2><p>Demandez le numéro RCCM et le numéro identifiant unique. Une entreprise qui hésite à les communiquer n'existe probablement pas légalement, et vous n'aurez aucun recours.</p><h2>2. L'attestation de garantie décennale</h2><p>C'est la pièce la plus importante et la plus souvent absente. Demandez l'attestation en cours de validité, avec le nom de l'assureur et le numéro de police. Vérifiez que la période couverte inclut la date prévue de vos travaux.</p><h2>3. La visite d'un chantier en cours</h2><p>Pas un chantier terminé et photographié : un chantier en activité. Vous y verrez l'ordre du stockage, le port des équipements de protection, la propreté des cheminements. Une entreprise qui refuse cette visite vous dit quelque chose.</p><h2>4. Le détail du devis</h2><p>Un devis en trois lignes n'est pas un devis, c'est une intention. Exigez un devis descriptif quantitatif : chaque poste décrit, sa quantité, son prix unitaire. C'est aussi ce qui vous permettra de comparer honnêtement deux offres.</p><h2>5. L'échéancier de paiement</h2><p>Le paiement doit suivre l'avancement, jamais le précéder de beaucoup. Un acompte de démarrage de 20 à 30 % est normal. Un acompte de 60 % avant le premier coup de pioche ne l'est pas.</p><h2>6. Les références vérifiables</h2><p>Demandez trois clients à appeler, pas trois photos. Un constructeur sérieux a des clients qui acceptent de témoigner, et il vous donnera leurs numéros sans difficulté.</p>",
                'body_en' => '<p>Every year we take over two or three projects abandoned by another contractor. The pattern is almost always the same: a quote clearly cheaper than the others, large payments requested early, and works stopping at the point where the remaining balance no longer covers the remaining work.</p><h2>1. The trade register and tax number</h2><p>Ask for the RCCM number and the unique taxpayer number. A company that hesitates to provide them probably does not legally exist, and you will have no recourse.</p><h2>2. The ten-year warranty certificate</h2><p>This is the most important document and the most often missing. Ask for the certificate in force, with the insurer name and policy number. Check that the covered period includes your planned construction dates.</p><h2>3. A visit to a live site</h2><p>Not a finished, photographed project: a site in operation. You will see how materials are stored, whether protective equipment is worn, how clean the walkways are. A company that refuses that visit is telling you something.</p><h2>4. The detail in the quote</h2><p>A three-line quote is not a quote, it is an intention. Insist on a priced bill of quantities: every item described, its quantity, its unit rate. It is also the only way to compare two offers honestly.</p><h2>5. The payment schedule</h2><p>Payment should follow progress, never run far ahead of it. A 20 to 30% mobilisation payment is normal. A 60% payment before the first excavation is not.</p><h2>6. Verifiable references</h2><p>Ask for three clients to call, not three photographs. A serious contractor has clients willing to speak, and will hand over their numbers without difficulty.</p>',
            ],
            [
                'slug' => 'apprentissage-interne-compagnons',
                'post_category_id' => $ids['vie-entreprise'],
                'title_fr' => 'Former sur le chantier : vingt apprentis par an, et pourquoi nous continuons',
                'title_en' => 'Training on site: twenty apprentices a year, and why we keep going',
                'reading_minutes' => 5,
                'published_at' => now()->subDays(62),
                'views' => 534,
                'tags' => ['formation', 'emploi', 'apprentissage'],
                'excerpt_fr' => "Le déficit de main-d'oeuvre qualifiée est la contrainte la plus sérieuse du secteur camerounais. Nous avons cessé d'attendre qu'elle se règle ailleurs.",
                'excerpt_en' => 'The shortage of skilled labour is the most serious constraint in the Cameroonian sector. We stopped waiting for someone else to solve it.',
                'cover_image' => 'blog/covers/apprentissage-interne-compagnons.jpg',
                'body_fr' => "<p>Trouver un coffreur expérimenté à Douala prend aujourd'hui entre trois et six semaines. Un ferrailleur capable de lire un plan de ferraillage complexe, davantage encore. Ce n'est pas une question de salaire : ces profils n'existent pas en nombre suffisant.</p><h2>Ce que nous faisons</h2><p>Depuis 2017, nous accueillons chaque année entre dix-huit et vingt-quatre apprentis sur nos chantiers, en alternance avec les centres de formation professionnelle de Douala et de Nkongsamba. La formation dure dix-huit mois et couvre quatre métiers : coffrage, ferraillage, maçonnerie et électricité du bâtiment.</p><p>Chaque apprenti est rattaché à un compagnon référent qui n'encadre jamais plus de deux apprentis simultanément. C'est la condition d'une transmission réelle plutôt que d'une main-d'oeuvre d'appoint déguisée.</p><h2>Ce que cela coûte, ce que cela rapporte</h2><p>La productivité d'un apprenti en première année est faible, et l'encadrement mobilise du temps de compagnon. Le coût net est réel. En contrepartie, 61 % des apprentis formés depuis 2017 travaillent aujourd'hui chez nous en contrat à durée indéterminée, et nous ne recrutons pratiquement plus de coffreurs à l'extérieur.</p><h2>Une limite honnête</h2><p>Vingt apprentis par an ne règlent pas un déficit sectoriel qui se compte en milliers de postes. Cela règle le nôtre, et cela forme des gens qui, s'ils partent, iront exercer ailleurs avec un vrai métier. Nous considérons que c'est déjà une contribution défendable.</p>",
                'body_en' => '<p>Finding an experienced formwork carpenter in Douala currently takes three to six weeks. A steel fixer able to read a complex reinforcement drawing takes longer still. This is not a question of pay: these people do not exist in sufficient numbers.</p><h2>What we do</h2><p>Since 2017 we have taken on between eighteen and twenty-four apprentices a year on our sites, alternating with the vocational training centres in Douala and Nkongsamba. The programme runs eighteen months and covers four trades: formwork, steel fixing, masonry and building electrics.</p><p>Each apprentice is attached to a mentor tradesperson who never supervises more than two apprentices at once. That is the condition for real transmission rather than disguised cheap labour.</p><h2>What it costs, what it returns</h2><p>A first-year apprentice has low productivity, and mentoring takes tradesperson time. The net cost is real. In return, 61% of the apprentices trained since 2017 work for us today on permanent contracts, and we barely recruit formwork carpenters externally any more.</p><h2>An honest limit</h2><p>Twenty apprentices a year does not fix a sector shortage counted in thousands of posts. It fixes ours, and it trains people who, if they leave, will practise a real trade elsewhere. We consider that a defensible contribution in itself.</p>',
            ],
            [
                'slug' => 'cout-construction-douala-2026',
                'post_category_id' => $ids['techniques-materiaux'],
                'title_fr' => 'Combien coûte réellement un mètre carré à Douala en 2026',
                'title_en' => 'What a square metre really costs in Douala in 2026',
                'reading_minutes' => 9,
                'is_featured' => true,
                'published_at' => now()->subDays(2),
                'views' => 2107,
                'tags' => ['coûts', 'budget', 'marché'],
                'excerpt_fr' => "Les fourchettes réelles par typologie, ce que recouvre le prix au mètre carré, et les cinq postes qui font varier un budget de 40 % à programme identique.",
                'excerpt_en' => 'Real ranges by building type, what a square-metre rate actually covers, and the five items that swing a budget by 40% on an identical brief.',
                'cover_image' => 'blog/covers/cout-construction-douala-2026.jpg',
                'body_fr' => "<p>La question revient à chaque premier rendez-vous, et la réponse honnête commence par une précision : le prix au mètre carré n'est pas une donnée d'entrée, c'est un résultat. Il se constate après le chiffrage, il ne se décide pas avant.</p><p>Cela dit, des ordres de grandeur existent, et les cacher ne rend service à personne. Voici les fourchettes que nous constatons sur nos propres opérations à Douala au premier trimestre 2026, hors foncier, hors honoraires d'architecte et hors taxes.</p><h2>Fourchettes par typologie</h2><ul><li><strong>Villa économique, finitions simples</strong> : 165 000 à 215 000 FCFA/m²</li><li><strong>Villa de standing, finitions soignées</strong> : 280 000 à 420 000 FCFA/m²</li><li><strong>Immeuble de logements R+4 et plus</strong> : 240 000 à 340 000 FCFA/m²</li><li><strong>Bureaux, second oeuvre standard</strong> : 310 000 à 450 000 FCFA/m²</li><li><strong>Entrepôt logistique simple</strong> : 145 000 à 210 000 FCFA/m²</li><li><strong>Bâtiment de santé</strong> : 480 000 à 700 000 FCFA/m²</li></ul><h2>Les cinq postes qui font varier le budget</h2><p><strong>1. Les fondations.</strong> Entre une semelle filante sur bon sol et un système de pieux forés, l'écart peut atteindre 18 % du coût total du bâtiment. C'est la raison pour laquelle une étude de sol à 1,2 million de francs est l'investissement le plus rentable d'un projet.</p><p><strong>2. Les finitions.</strong> Le poste le plus élastique. Un carrelage à 8 000 FCFA/m² et un grès cérame à 45 000 FCFA/m² remplissent la même fonction technique.</p><p><strong>3. La climatisation.</strong> Un bâtiment conçu passivement peut économiser l'intégralité de ce poste, soit 8 à 14 % du budget selon la typologie.</p><p><strong>4. L'accès au site.</strong> Une parcelle inaccessible aux camions toupies impose un bétonnage à la bétonnière, qui multiplie par 2,5 le coût de main-d'oeuvre du gros oeuvre.</p><p><strong>5. La durée.</strong> Un chantier étalé sur trente mois au lieu de dix-huit supporte douze mois de frais généraux supplémentaires, soit environ 6 % du montant des travaux.</p><h2>Ce que le prix au mètre carré ne dit pas</h2><p>Il ne dit rien de la durée de vie de l'ouvrage. Deux bâtiments construits au même prix peuvent avoir des coûts d'entretien à dix ans qui varient du simple au triple, selon la qualité de l'étanchéité, de l'enrobage des aciers et des menuiseries. C'est la raison pour laquelle nous chiffrons toujours en indiquant ce que nous excluons volontairement, et pourquoi.</p>",
                'body_en' => '<p>The question comes up at every first meeting, and the honest answer starts with a clarification: the square-metre rate is not an input, it is an output. You observe it after pricing, you do not decide it beforehand.</p><p>That said, orders of magnitude exist, and hiding them serves nobody. Here are the ranges we see on our own projects in Douala in the first quarter of 2026, excluding land, architect fees and tax.</p><h2>Ranges by building type</h2><ul><li><strong>Economy villa, simple finishes</strong>: 165,000 to 215,000 XAF/m²</li><li><strong>Premium villa, high-quality finishes</strong>: 280,000 to 420,000 XAF/m²</li><li><strong>Apartment building, five storeys and above</strong>: 240,000 to 340,000 XAF/m²</li><li><strong>Offices, standard fit-out</strong>: 310,000 to 450,000 XAF/m²</li><li><strong>Simple logistics warehouse</strong>: 145,000 to 210,000 XAF/m²</li><li><strong>Healthcare building</strong>: 480,000 to 700,000 XAF/m²</li></ul><h2>The five items that swing the budget</h2><p><strong>1. Foundations.</strong> Between a strip footing on good ground and a bored pile system, the gap can reach 18% of the total building cost. This is why a 1.2 million franc soil survey is the highest-return investment on a project.</p><p><strong>2. Finishes.</strong> The most elastic item. Tiling at 8,000 XAF/m² and porcelain stoneware at 45,000 XAF/m² perform the same technical function.</p><p><strong>3. Air conditioning.</strong> A passively designed building can save this item entirely, 8 to 14% of the budget depending on the type.</p><p><strong>4. Site access.</strong> A plot inaccessible to ready-mix trucks forces site-mixed concrete, which multiplies structural labour cost by 2.5.</p><p><strong>5. Duration.</strong> A project stretched over thirty months instead of eighteen carries twelve extra months of overheads, around 6% of the works value.</p><h2>What the square-metre rate does not tell you</h2><p>It says nothing about how long the building will last. Two buildings built for the same price can have ten-year maintenance costs varying threefold, depending on the quality of the waterproofing, the reinforcement cover and the joinery. That is why we always price while stating what we have deliberately excluded, and why.</p>',
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(['slug' => $post['slug']], $post + ['author_id' => $author?->id]);
        }
    }

    protected function faqs(): void
    {
        $faqs = [
            [
                'group' => 'general', 'position' => 1,
                'question_fr' => 'Dans quelles régions du Cameroun intervenez-vous ?',
                'question_en' => 'Which regions of Cameroon do you work in?',
                'answer_fr' => "Notre siège et notre parc matériel sont à Douala, et nous disposons de bases permanentes à Yaoundé et à Kribi. Nous intervenons couramment dans le Littoral, le Centre, le Sud et le Sud-Ouest. Au-delà, nous étudions les demandes au cas par cas : la faisabilité dépend surtout de la logistique d'approvisionnement et de la durée du chantier.",
                'answer_en' => 'Our head office and plant depot are in Douala, with permanent bases in Yaounde and Kribi. We work routinely across the Littoral, Centre, South and South-West regions. Beyond that we review requests case by case: feasibility depends mainly on supply logistics and project duration.',
            ],
            [
                'group' => 'general', 'position' => 2,
                'question_fr' => 'Travaillez-vous avec des clients de la diaspora ?',
                'question_en' => 'Do you work with clients based abroad?',
                'answer_fr' => "Oui, c'est une part significative de notre activité résidentielle. Nous avons mis en place un protocole spécifique : rapport photographique daté chaque semaine, visioconférence de chantier bimensuelle, transmission systématique des factures fournisseurs et validation écrite avant chaque phase engageante. Un compte séquestre peut être ouvert pour les approvisionnements si le client le souhaite.",
                'answer_en' => 'Yes, it is a significant part of our residential work. We run a specific protocol: a dated photo report every week, a fortnightly site video call, systematic forwarding of supplier invoices and written sign-off before each committing phase. An escrow account can be opened for material purchases if the client prefers.',
            ],
            [
                'group' => 'general', 'position' => 3,
                'question_fr' => 'Êtes-vous couverts par une garantie décennale ?',
                'question_en' => 'Are you covered by a ten-year warranty?',
                'answer_fr' => "Oui. Nous sommes assurés en responsabilité civile décennale auprès de Chanas Assurances, et l'attestation en cours de validité est remise à chaque maître d'ouvrage lors de la réception. Nous encourageons d'ailleurs tout client à exiger cette pièce de n'importe quelle entreprise avant de signer.",
                'answer_en' => 'Yes. We hold ten-year structural liability cover with Chanas Assurances, and the certificate in force is handed to every client at completion. We would in fact encourage any client to demand that document from any contractor before signing.',
            ],
            [
                'group' => 'quote', 'position' => 4,
                'question_fr' => 'Le devis est-il payant ?',
                'question_en' => 'Do you charge for a quote?',
                'answer_fr' => "Non pour une estimation budgétaire et une visite technique dans l'agglomération de Douala : c'est gratuit et sans engagement. Un devis descriptif quantitatif détaillé nécessite en revanche un travail d'études réel. Lorsqu'il porte sur un programme complexe, nous facturons cette étude, et le montant est déduit du marché si les travaux nous sont confiés.",
                'answer_en' => 'No, not for a budget estimate and a technical visit within greater Douala: that is free and without obligation. A detailed priced bill of quantities does require genuine design work, however. For complex programmes we charge for that study, and the amount is credited against the contract if the works are awarded to us.',
            ],
            [
                'group' => 'quote', 'position' => 5,
                'question_fr' => 'Sous quel délai recevrai-je une réponse ?',
                'question_en' => 'How quickly will I get a response?',
                'answer_fr' => "Un ingénieur vous rappelle sous 48 heures ouvrées après réception de votre demande. La visite technique se planifie généralement dans la semaine qui suit. Le devis détaillé arrive ensuite sous une à trois semaines selon la complexité du programme.",
                'answer_en' => 'An engineer calls you back within 48 working hours of receiving your request. The technical visit is usually scheduled within the following week. The detailed quote then follows within one to three weeks depending on the complexity of the brief.',
            ],
            [
                'group' => 'quote', 'position' => 6,
                'question_fr' => 'Quelles sont vos modalités de paiement ?',
                'question_en' => 'What are your payment terms?',
                'answer_fr' => "Un acompte de démarrage compris entre 20 et 30 %, puis des situations mensuelles calculées sur l'avancement réel, constatées contradictoirement. Une retenue de garantie de 5 % est libérée à la levée des réserves. Nous ne demandons jamais un paiement qui dépasse significativement l'avancement des travaux.",
                'answer_en' => 'A mobilisation payment of 20 to 30%, then monthly valuations calculated on actual progress and agreed jointly. A 5% retention is released once snags are cleared. We never request a payment that runs significantly ahead of progress on site.',
            ],
            [
                'group' => 'process', 'position' => 7,
                'question_fr' => 'Pouvez-vous vous charger du permis de construire ?',
                'question_en' => 'Can you handle the building permit?',
                'answer_fr' => "Oui. Nous montons le dossier complet, le déposons auprès de la commune d'arrondissement compétente et suivons l'instruction jusqu'à l'obtention, y compris les demandes de pièces complémentaires. Comptez environ huit semaines pour un dossier complet à Douala, davantage si le titre foncier doit être régularisé au préalable.",
                'answer_en' => 'Yes. We assemble the full application, file it with the relevant district council and follow the process through to approval, including any requests for additional documents. Allow around eight weeks for a complete file in Douala, longer if the land title needs regularising first.',
            ],
            [
                'group' => 'process', 'position' => 8,
                'question_fr' => 'Que se passe-t-il si le chantier prend du retard ?',
                'question_en' => 'What happens if the project runs late?',
                'answer_fr' => "Le contrat fixe une date de livraison et des pénalités de retard qui s'appliquent à nous. Les seules causes de prolongation admises sont les cas de force majeure, les intempéries exceptionnelles documentées et les modifications demandées par le maître d'ouvrage, chacune faisant l'objet d'un avenant écrit. En seize ans, 98 % de nos chantiers ont été livrés dans les délais contractuels.",
                'answer_en' => 'The contract sets a completion date and liquidated damages that apply to us. The only accepted grounds for extension are force majeure, documented exceptional weather and client-requested changes, each covered by a written variation. Over sixteen years, 98% of our projects have been delivered within the contractual programme.',
            ],
            [
                'group' => 'process', 'position' => 9,
                'question_fr' => 'Puis-je visiter le chantier pendant les travaux ?',
                'question_en' => 'Can I visit the site during construction?',
                'answer_fr' => "Bien sûr, et nous vous y encourageons. Les visites se planifient avec le conducteur de travaux pour des raisons de sécurité, et nous fournissons casque et gilet. Un point d'avancement formel a lieu chaque semaine, auquel vous êtes systématiquement convié.",
                'answer_en' => 'Of course, and we encourage it. Visits are arranged with the site manager for safety reasons, and we provide a helmet and hi-vis vest. There is a formal weekly progress review, to which you are always invited.',
            ],
            [
                'group' => 'services', 'position' => 10,
                'question_fr' => 'Acceptez-vous les petits chantiers ?',
                'question_en' => 'Do you take on small projects?',
                'answer_fr' => "Oui, à partir d'environ 25 millions de francs. En dessous, notre structure de coûts ne nous permet pas d'être compétitifs, et nous préférons vous orienter vers un artisan qualifié plutôt que de vous faire une offre que vous trouverez chère à juste titre.",
                'answer_en' => 'Yes, from around 25 million francs upward. Below that our cost structure does not let us be competitive, and we would rather point you to a qualified tradesperson than make you an offer you would rightly find expensive.',
            ],
            [
                'group' => 'services', 'position' => 11,
                'question_fr' => 'Travaillez-vous avec l\'architecte de mon choix ?',
                'question_en' => 'Will you work with the architect of my choice?',
                'answer_fr' => "Oui, sans difficulté. Nous intervenons régulièrement en entreprise générale sous la maîtrise d'oeuvre d'un architecte extérieur. Notre bureau d'études prend alors en charge la partie structure et les plans d'exécution, en coordination avec lui. Si vous n'avez pas encore d'architecte, nous pouvons vous en recommander plusieurs sans exclusivité.",
                'answer_en' => 'Yes, without difficulty. We regularly work as main contractor under an external architect. Our engineering office then takes on the structural design and construction drawings, in coordination with them. If you do not yet have an architect, we can recommend several with no exclusivity.',
            ],
            [
                'group' => 'services', 'position' => 12,
                'question_fr' => 'Reprenez-vous des chantiers abandonnés par une autre entreprise ?',
                'question_en' => 'Do you take over projects abandoned by another contractor?',
                'answer_fr' => "Oui, c'est une situation que nous rencontrons plusieurs fois par an. Nous commençons systématiquement par un diagnostic de l'existant : contrôle du ferraillage en place, essais sur le béton coulé, vérification de la conformité aux plans. Ce diagnostic est facturé et il est indispensable : nous ne pouvons pas garantir un ouvrage dont nous n'avons pas vérifié les fondations.",
                'answer_en' => 'Yes, it is a situation we encounter several times a year. We always start with a survey of what is there: checking the installed reinforcement, testing the poured concrete, verifying compliance with the drawings. That survey is chargeable and it is essential: we cannot warrant a structure whose foundations we have not verified.',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question_fr' => $faq['question_fr']], $faq);
        }
    }
}
