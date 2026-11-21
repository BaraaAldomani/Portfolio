<?php

/**
 * Single source of truth for owner contact details and external links.
 * Referenced from Blade, JSON-LD, and the contact page — never hardcode
 * these values anywhere else.
 */
return [
    /*
     * The one address the site answers on, e.g. https://baraadom.com. When set,
     * every other host this folder is served under (www, an older domain)
     * redirects to it and every generated link uses it. The deploy writes it
     * into the server .env from PRIMARY_URL in the workflow file; it stays
     * empty for local work and tests.
     */
    'primary_url' => env('PRIMARY_URL'),

    /*
     * TEMPORARY (set 21 Nov 2026, remove by setting to null). The host's CDN in
     * front of baraadom.com stored a few redirects it saw before the move
     * (/, /ar, /robots.txt -> the old address), so those pages bounced between
     * the two addresses. A redirect from another host now adds this query
     * parameter, which makes the address new to the CDN and ends the loop.
     * Once https://baraadom.com/ar answers 200 on its own (the stored copies
     * have expired, or the CDN cache was flushed in hPanel), set this to null.
     */
    'redirect_marker' => 'moved',

    /*
     * Share pictures have the site's address drawn into them, so each address
     * has its own pair. A host that is not listed uses images/og.png and
     * images/og-en.png.
     */
    'share_images' => [
        'baraadom.com' => ['ar' => 'images/og-baraadom.png', 'en' => 'images/og-baraadom-en.png'],
    ],

    'email' => 'baraa.aldomani@gmail.com',

    // Inbox that receives an alert for every contact-form message. Leave
    // empty to skip email alerts (Telegram alerts are configured in
    // config/services.php). Needs a working MAIL_* setup in .env.
    'lead_notify_email' => env('LEAD_NOTIFY_EMAIL'),

    // Digits only, international format — used to build the wa.me link.
    'whatsapp' => '966553785576',
    'phone_display' => '+966 55 378 5576',

    'linkedin' => 'https://www.linkedin.com/in/baraaaldomani/',
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
