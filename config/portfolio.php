<?php

/**
 * Single source of truth for owner contact details and external links.
 * Referenced from Blade, JSON-LD, and the contact page — never hardcode
 * these values anywhere else.
 */
return [
    'email' => 'baraa.aldomani@gmail.com',

    // Digits only, international format — used to build the wa.me link.
    'whatsapp' => '966553785576',
    'phone_display' => '+966 55 378 5576',

    'linkedin' => 'https://www.linkedin.com/in/baraa-aldomani/',
    'gitlab' => 'https://gitlab.com/BaraaAldomani',

    // Public path (under public/) to the downloadable CV.
    'cv_path' => 'cv/baraa-aldomani-cv.pdf',

    'experience_years' => 4,

    /*
     * Imagery — single source of truth for photo paths (under public/).
     * These ship as branded SVG placeholders; to use a real photo later,
     * drop the file in public/images/... and point the key at it (the
     * extension can change freely, e.g. 'images/about-portrait.jpg').
     */
    'images' => [
        'hero_portrait' => 'images/hero-portrait.svg',
        'about_portrait' => 'images/baraa-about.jpg',
        'services' => [
            'systems' => 'images/services/systems.svg',
            'web_apps' => 'images/services/web_apps.svg',
            'websites' => 'images/services/websites.svg',
            'apis' => 'images/services/apis.svg',
        ],
        // Project covers are resolved by slug: images/projects/{slug_en}.svg
        'projects_dir' => 'images/projects',

        // v3 — brand logo + real photos to drop in later.
        'logo' => 'images/logo.svg',
        'rakeez_logo' => 'images/rakeez-logo.svg',
        'portrait' => 'images/baraa-portrait.png',
        'workspace' => 'images/workspace.svg',
    ],
];
