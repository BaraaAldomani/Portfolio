<?php

/*
 * The six service pages (/{locale}/services/{slug}). One page per offer in
 * config/pricing.php; the words live in lang/{locale}/service_pages.php.
 *
 * `proof` names the real projects shown on each page, by projects.slug_en and
 * in the order they appear. A project that is not in the database is simply
 * skipped, so a page never breaks when work is added or removed from the
 * dashboard. Only list a project under a service it really is an example of.
 */
return [
    'proof' => [
        'site' => ['business-consultant-website', 'executive-chef-website'],
        'platform' => ['sajjah-diwaniya-booking-platform', 'shattib-finishing-renovation-platform', 'printly-print-on-demand-marketplace'],
        'system' => ['shattib-finishing-renovation-platform'],
        'app' => ['eyadti-medical-booking-system'],
        'api' => ['eyadti-medical-booking-system', 'printly-print-on-demand-marketplace', 'learny-language-learning-platform'],
        'ai' => [],
    ],
];
