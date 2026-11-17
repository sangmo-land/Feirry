<?php

return [
    'seo' => [
        'home' => ['description' => 'FEIRRY Construction Designs: architecture, engineering, 3D design and construction in Douala and Yaoundé. From the first idea to the handover of the keys.'],
        'feirry' => ['title' => 'The company', 'description' => 'FEIRRY Construction Designs, a design and construction firm in Douala: mission, integrated approach, commitments and a five-stage process.'],
        'expertises' => ['title' => 'Expertise', 'description' => 'Six areas of expertise: land and feasibility, geotechnics, architecture and engineering, 3D design, construction and finishes, supervision and expertise.'],
        'projets' => ['title' => 'Projects', 'description' => 'Projects designed, built or supervised by FEIRRY in Douala and West Cameroon, with the exact role FEIRRY played on each.'],
        'offres' => ['title' => 'Offers', 'description' => 'Three packages to start your project: Plans, Plans + 3D and Turnkey. Prices on request, depending on floor area and programme.'],
        'cdp' => ['title' => 'Coup de Pouce FEIRRY', 'description' => 'The Coup de Pouce FEIRRY programme: a free review of your file or a technical visit to your land, during announced sessions.'],
        'conseils' => ['title' => 'Advice & news', 'description' => 'Practical guides to prepare your building project in Cameroon: land, soil studies, building from abroad.'],
        'projet' => ['title' => 'My project', 'description' => 'Describe your project in five short steps. A FEIRRY advisor will get back to you to discuss it.'],
    ],
    'attributes' => [
        'need' => 'type of need', 'location' => 'location', 'programme' => 'programme', 'phase' => 'stage',
        'size' => 'floor area', 'budget_range' => 'budget', 'name' => 'name', 'phone' => 'phone', 'email' => 'email',
        'contact_preference' => 'contact preference', 'message' => 'message', 'attachments' => 'documents',
        'attachments.*' => 'document', 'privacy' => 'consent', 'path' => 'path', 'city' => 'city',
        'project_type' => 'project type', 'description' => 'description', 'meeting_mode' => 'meeting preference',
        'site_location' => 'location of the land', 'objective' => 'purpose', 'access_constraints' => 'access constraints',
        'availability' => 'availability',
    ],
    'errors' => [
        'phone' => 'Please enter a valid number (at least 9 digits).',
        'contact' => 'Please give a phone number or an email address.',
        'privacy' => 'We need your consent to process your request.',
    ],
];
