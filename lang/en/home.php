<?php

return [
    'hero' => [
        'eyebrow' => 'Freelance Software Engineer · Riyadh',
        'title' => 'I turn your idea into software that works.',
        'lead' => 'Software engineer with 4+ years building reliable web & mobile apps, management systems, and integrations. From first idea to launch, I handle the engineering so you can focus on your business.',
        'stats' => [
            'experience' => 'Years of experience',
            'projects' => 'Projects delivered',
            'sectors' => 'Industries served',
        ],
    ],
    'terminal' => [
        'title' => 'engineer.php',
        'aria_label' => 'Terminal window typing code',
        'lines' => [
            ['type' => 'comment', 'text' => "// I'm Baraa, a software engineer who turns ideas into products"],
            ['type' => 'code', 'text' => '$engineer = new SoftwareEngineer('],
            ['type' => 'code', 'text' => "    role: 'Freelance Software Engineer',"],
            ['type' => 'code', 'text' => "    based: 'Riyadh, Saudi Arabia',"],
            ['type' => 'code', 'text' => ');'],
            ['type' => 'blank', 'text' => ''],
            ['type' => 'comment', 'text' => '// tell me what your business needs'],
            ['type' => 'code', 'text' => "\$engineer->build('your idea')->launch();"],
            ['type' => 'output', 'text' => '✓ Delivered, reliable, secure, built to scale'],
        ],
    ],
    'services' => [
        'eyebrow' => 'What I do',
        'title' => 'Services covering your project from idea to launch',
        'lead' => 'I work directly with you and translate what your business needs into a clear, reliable product.',
        'items' => [
            'systems' => [
                'title' => 'Custom Management Systems',
                'blurb' => 'Systems built around how your business works: operations, roles, and reporting that support your decisions.',
            ],
            'web_apps' => [
                'title' => 'Web & Mobile Apps',
                'blurb' => 'Complete platforms that reach your customers on the browser and on their phones, fast and secure.',
            ],
            'websites' => [
                'title' => 'Professional Websites',
                'blurb' => 'Bilingual sites optimized for search and speed that represent your brand well.',
            ],
            'apis' => [
                'title' => 'Integrations & APIs',
                'blurb' => 'Connecting your systems to payment gateways and external services reliably and securely.',
            ],
        ],
    ],
    'projects' => [
        'eyebrow' => 'Selected work',
        'title' => 'Real projects, measurable results',
        'lead' => 'Every project is a story: a challenge the client faced, an engineered solution, and a tangible result.',
    ],
    'cta' => [
        'title' => 'Have a project in mind?',
        'lead' => "Tell me about it and I'll suggest the clearest way to build it, the first consultation is free.",
        'button' => 'Get in touch',
    ],
];
