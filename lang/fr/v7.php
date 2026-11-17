<?php

return [
    'seo' => [
        'home' => ['description' => 'FEIRRY Construction Designs : architecture, ingénierie, design 3D et construction à Douala et Yaoundé. De la première idée à la remise des clés.'],
        'feirry' => ['title' => 'L’entreprise', 'description' => 'FEIRRY Construction Designs, bureau de conception et de construction à Douala : mission, approche intégrée, engagements et méthode en cinq étapes.'],
        'expertises' => ['title' => 'Expertises', 'description' => 'Six familles d’expertise : foncier et faisabilité, géotechnique, architecture et ingénierie, design 3D, construction et finitions, contrôle et expertise.'],
        'projets' => ['title' => 'Projets', 'description' => 'Projets conçus, construits ou suivis par FEIRRY à Douala et dans l’Ouest du Cameroun, avec la mission exacte assurée sur chacun.'],
        'offres' => ['title' => 'Offres', 'description' => 'Trois formules pour démarrer votre projet : Plans, Plans + 3D et Clé en main. Tarifs sur devis selon surface et programme.'],
        'cdp' => ['title' => 'Coup de Pouce FEIRRY', 'description' => 'Le programme Coup de Pouce FEIRRY : consultation gratuite de votre dossier ou visite technique de votre terrain, selon les sessions annoncées.'],
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
    'errors' => [
        'phone' => 'Indiquez un numéro valide (au moins 9 chiffres).',
        'contact' => 'Indiquez un téléphone ou une adresse e-mail.',
        'privacy' => 'Votre accord est nécessaire pour que nous traitions votre demande.',
    ],
];
