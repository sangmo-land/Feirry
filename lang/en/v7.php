<?php

return [
    'seo' => [
        'home' => ['description' => 'FEIRRY Construction Designs: architecture, engineering, 3D design and construction in Douala and Yaoundé. From the first idea to the handover of the keys.'],
        'feirry' => ['title' => 'The company', 'description' => 'FEIRRY Construction Designs, a design and construction firm in Douala: mission, integrated approach, commitments and a five-stage process.'],
        'expertises' => ['title' => 'Expertise', 'description' => 'Six areas of expertise: land and feasibility, geotechnics, architecture and engineering, 3D design, construction and finishes, supervision and expertise.'],
        'projets' => ['title' => 'Projects', 'description' => 'Projects designed, built or supervised by FEIRRY in Douala and West Cameroon, with the exact role FEIRRY played on each.'],
        'offres' => ['title' => 'Offers', 'description' => 'Three packages to start your project: Plans, Plans + 3D and Turnkey. Prices on request, depending on floor area and programme.'],
        'cdp' => ['title' => 'Coup de Pouce FEIRRY', 'description' => 'An expert eye to build better: a free preliminary review of your file or a site visit in Douala, after selection by FEIRRY.'],
        'cdp_consultation' => ['title' => 'Have my file reviewed — Coup de Pouce', 'description' => 'Ask the FEIRRY team for a free preliminary review of your drawings or project. Two steps, no account.'],
        'cdp_visit' => ['title' => 'Request a site visit — Coup de Pouce', 'description' => 'Ask for a free first look at your building site in Douala. FEIRRY contacts you to set a date if your request is selected.'],
        'cdp_confirmation' => ['title' => 'Request recorded — Coup de Pouce'],
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
    'cdp_attributes' => [
        'city' => 'city or neighbourhood', 'project_type' => 'project type', 'progress' => 'stage',
        'land_area' => 'plot area', 'footprint_area' => 'built footprint', 'levels' => 'number of storeys',
        'description' => 'your question', 'site_location' => 'landmark or location', 'gps' => 'GPS coordinates',
        'availability' => 'availability', 'access_constraints' => 'access information', 'phone' => 'phone / WhatsApp',
        'idempotency_key' => 'submission key',
    ],
    'errors' => [
        'file_type' => 'This file type is not supported. Accepted formats: PDF for drawings; JPG, PNG or WebP for photos.',
        'file_size' => 'This file is larger than allowed. You can continue without it and send it later.',
        'phone' => 'Please enter a valid number (at least 9 digits).',
        'contact' => 'Please give a phone number or an email address.',
        'privacy' => 'We need your consent to process your request.',
    ],
];
