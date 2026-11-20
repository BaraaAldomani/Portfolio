<?php

/**
 * Single source of truth for owner contact details and external links.
 * Referenced from Blade, JSON-LD, and the contact page — never hardcode
 * these values anywhere else.
 */
return [
    'email' => 'baraa.aldomani@gmail.com',

    // Inbox that receives an alert for every contact-form message. Leave
    // empty to skip email alerts (Telegram alerts are configured in
    // config/services.php). Needs a working MAIL_* setup in .env.
    'lead_notify_email' => env('LEAD_NOTIFY_EMAIL'),

    // Digits only, international format — used to build the wa.me link.
    'whatsapp' => '966553785576',
    'phone_display' => '+966 55 378 5576',

    'linkedin' => 'https://www.linkedin.com/in/baraa-aldomani/',
    'gitlab' => 'https://gitlab.com/BaraaAldomani',

    // Extra profiles — fill in from the dashboard (Contact & social). Left blank
    // here so the identity graph (JSON-LD sameAs) only lists links that exist.
    'github' => '',
    'twitter' => '',
    'stackoverflow' => '',
    'youtube' => '',

    // Public path (under public/) to the downloadable CV.
    'cv_path' => 'cv/baraa-aldomani-cv.pdf',

    'experience_years' => 4,

    /*
     * Imagery: default photo paths (under public/). Each can be replaced from
     * the dashboard (Images); the upload then wins over these defaults.
     * Project screenshots live on each project (dashboard → Projects).
     */
    'images' => [
        'about_portrait' => 'images/baraa-about.webp',
        // Used for the Person structured data (search engines), not on a page.
        'portrait' => 'images/baraa-portrait.webp',
    ],
];
