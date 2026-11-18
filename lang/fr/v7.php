<?php

return [
    'seo' => [
        'home' => ['description' => 'FEIRRY Construction Designs : architecture, ingénierie, design 3D et construction à Douala et Yaoundé. De la première idée à la remise des clés.'],
        'feirry' => ['title' => 'L’entreprise', 'description' => 'FEIRRY Construction Designs, bureau de conception et de construction à Douala : mission, approche intégrée, engagements et méthode en cinq étapes.'],
        'expertises' => ['title' => 'Expertises', 'description' => 'Six familles d’expertise : foncier et faisabilité, géotechnique, architecture et ingénierie, design 3D, construction et finitions, contrôle et expertise.'],
        'projets' => ['title' => 'Projets', 'description' => 'Projets conçus, construits ou suivis par FEIRRY à Douala et dans l’Ouest du Cameroun, avec la mission exacte assurée sur chacun.'],
        'offres' => ['title' => 'Offres', 'description' => 'Trois formules pour démarrer votre projet : Plans, Plans + 3D et Clé en main. Tarifs sur devis selon surface et programme.'],
        'cdp' => ['title' => 'Le Coup de Pouce FEIRRY', 'description' => 'Un regard d’expert pour mieux construire : consultation préliminaire gratuite de votre dossier ou visite de chantier à Douala, après sélection par FEIRRY.'],
        'cdp_consultation' => ['title' => 'Faire examiner mon dossier — Coup de Pouce', 'description' => 'Demandez un examen préliminaire gratuit de vos plans ou de votre projet par l’équipe FEIRRY. Deux étapes, sans compte.'],
        'cdp_visit' => ['title' => 'Demander une visite de chantier — Coup de Pouce', 'description' => 'Demandez une première observation gratuite de votre chantier à Douala. FEIRRY vous recontacte pour fixer une date si la demande est retenue.'],
        'cdp_confirmation' => ['title' => 'Demande enregistrée — Coup de Pouce'],
        'conseils' => ['title' => 'Conseils & actualités', 'description' => 'Guides pratiques pour préparer votre projet de construction au Cameroun : terrain, études de sol, construire depuis l’étranger.'],
        'projet' => ['title' => 'Mon projet', 'description' => 'Décrivez votre projet en cinq étapes courtes. Un conseiller FEIRRY vous recontacte pour en parler.'],
    ],
    'attributes' => [
        'need' => 'type de besoin', 'location' => 'localisation', 'programme' => 'programme', 'phase' => 'phase',
        'size' => 'surface', 'budget_range' => 'enveloppe', 'name' => 'nom', 'phone' => 'téléphone', 'email' => 'e-mail',
        'contact_preference' => 'modalité de contact', 'message' => 'message', 'attachments' => 'documents',
        'attachments.*' => 'document', 'privacy' => 'consentement', 'path' => 'parcours', 'city' => 'ville',
        'project_type' => 'type de projet', 'description' => 'description', 'meeting_mode' => 'préférence de rendez-vous',
        'site_location' => 'localisation du terrain', 'objective' => 'objectif', 'access_constraints' => 'contraintes d’accès',
        'availability' => 'disponibilité',
    ],
    'cdp_attributes' => [
        'city' => 'ville ou quartier', 'project_type' => 'type de projet', 'progress' => 'avancement',
        'land_area' => 'surface du terrain', 'footprint_area' => 'emprise bâtie', 'levels' => 'nombre de niveaux',
        'description' => 'votre question', 'site_location' => 'repère ou localisation', 'gps' => 'coordonnées GPS',
        'availability' => 'disponibilités', 'access_constraints' => 'informations d’accès', 'phone' => 'téléphone / WhatsApp',
        'idempotency_key' => 'clé d’envoi',
    ],
    'errors' => [
        'file_type' => 'Ce fichier n’est pas pris en charge. Formats acceptés : PDF pour les plans ; JPG, PNG ou WebP pour les photos.',
        'file_size' => 'Ce fichier dépasse la taille autorisée. Vous pouvez poursuivre sans pièce et l’envoyer plus tard.',
        'phone' => 'Indiquez un numéro valide (au moins 9 chiffres).',
        'contact' => 'Indiquez un téléphone ou une adresse e-mail.',
        'privacy' => 'Votre accord est nécessaire pour que nous traitions votre demande.',
    ],
];
