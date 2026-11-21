<?php

/*
 * Public price guide (SAR). These are STARTING points shown on the site and
 * used by the project estimator; the final quote is always written per
 * project. Edit freely: the site and the estimator read only from here.
 */
return [
    // The day these prices were last checked. Shown on the service pages as
    // "prices last updated", so change it whenever you review the numbers.
    'reviewed' => '2026-11-21',

    // key => starting price, typical upper bound, and duration in weeks.
    'offers' => [
        'site' => ['from' => 3500, 'to' => 9000, 'weeks' => [1, 2]],
        'platform' => ['from' => 12000, 'to' => 35000, 'weeks' => [4, 8]],
        'system' => ['from' => 15000, 'to' => 45000, 'weeks' => [6, 10]],
        'app' => ['from' => 18000, 'to' => 50000, 'weeks' => [8, 12]],
        'api' => ['from' => 4000, 'to' => 15000, 'weeks' => [1, 3]],
        'ai' => ['from' => 5000, 'to' => 20000, 'weeks' => [1, 3]],
    ],

    // Optional add-ons in the estimator: amount added to the low end, and
    // extra weeks.
    'extras' => [
        'bilingual' => ['add' => 1500, 'weeks' => 0.5],
        'payments' => ['add' => 2500, 'weeks' => 1],
        'dashboard' => ['add' => 3000, 'weeks' => 1],
        'integrations' => ['add' => 3500, 'weeks' => 1],
        'ai' => ['add' => 4000, 'weeks' => 1],
        'content' => ['add' => 1500, 'weeks' => 0.5],
    ],

    // How pace changes price and time.
    'pace' => [
        'normal' => ['price' => 1.0, 'time' => 1.0],
        'fast' => ['price' => 1.25, 'time' => 0.7],
        'flex' => ['price' => 0.95, 'time' => 1.3],
    ],

    // The estimate's upper bound = low × spread, rounded to the nearest 500.
    'spread' => 1.45,
];
