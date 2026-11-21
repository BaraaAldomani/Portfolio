<?php

/*
 * Copy for the "It starts with a dot" design. Mirrors lang/ar/site.php key
 * for key. Rule: talk to the client about their problem; never brag.
 */
return [
    'nav' => [
        'services' => 'Services & prices',
        'work' => 'Work',
        'about' => 'About',
        'contact' => 'Contact',
        'start' => 'Start a project',
        'menu' => 'Menu',
        'close' => 'Close',
    ],

    'hero' => [
        'cta_estimate' => 'Price your project',
        'status' => 'Open for new projects',
        'role' => 'Freelance software engineer',
        'place' => 'Riyadh',
    ],

    // Variant B of the home page: the blueprint, written for founders.
    'b' => [
        'hero' => [
            'l1' => 'From your idea',
            'alt' => 'to',
            'l2' => 'a live',
            'l3' => 'product.',
            'lead' => 'For founders and new ventures: I build your digital product with you, from the first plan to launch day. A clear scope, a fixed price, a written timeline.',
            'scroll' => 'Scroll and follow the line',
        ],
        // The hero loop. `scene` names the real project each sentence became.
        'ideas' => [
            ['say' => 'I want a platform to book diwaniyas', 'steps' => ['Browse', 'Book', 'Pay', 'Confirm'], 'scene' => 'sajjah'],
            ['say' => 'One place for finishing and renovation', 'steps' => ['Request', 'Visit', 'Build', 'Hand over'], 'scene' => 'shattib'],
            ['say' => 'A site that presents me and brings clients', 'steps' => ['Services', 'Courses', 'Contact'], 'scene' => 'rouaa'],
            ['say' => 'A site worthy of my kitchen', 'steps' => ['Dishes', 'Experience', 'Contact'], 'scene' => 'nour'],
        ],
        'questions' => ['Where do I start?', 'What will it cost?', 'How long?', 'And after launch?'],
        // The word on the tab of the hero's prompt.
        'labels' => [
            'prompt' => 'Your idea',
        ],
        // The journey under the hero: one line that starts as a tangle and ends
        // as a rocket. Each stop is a stage of the work, said in one sentence,
        // with what the client receives at the end of it.
        'journey' => [
            'start' => 'The start',
            'step' => 'Step',
            'get' => 'You get',
            // What the tidy lines of the analysis stand for.
            'rows' => ['What we build', 'What it costs', 'When it is ready'],
            'steps' => [
                'idea' => ['title' => 'Your idea', 'text' => 'Every founder starts with an idea, and with more questions than ideas. You do not need to sort them out before we talk.'],
                'analysis' => ['title' => 'Analysis', 'text' => 'I learn your project and write down what we will build, what it costs and when it will be ready.', 'out' => 'A written quote at a fixed price'],
                'design' => ['title' => 'Design', 'text' => 'You see what your project looks like and approve it before it is built.', 'out' => 'The screen designs'],
                'build' => ['title' => 'Development and testing', 'text' => 'I build it and test it on phone and desktop, while you follow a preview.', 'out' => 'A link you follow every week'],
                'launch' => ['title' => 'Launch', 'text' => 'It goes live on your address, with training and follow-up after.', 'out' => 'Your project live, and everything in your name'],
            ],
            'landing' => [
                'title' => 'The first step is *free*.',
                'lead' => 'A call where I learn your project, and then the written quote reaches you.',
                'work' => 'Or see my work first',
            ],
        ],
    ],

    'offers' => [
        'kind' => 'Service',
        'tag' => 'What I build',
        'title' => 'Six things I build, with the prices *on the table*.',
        'lead' => 'The price shown is a starting point. Once I understand your project you get a written, fixed-price quote, and nothing starts before you approve it.',
        'from' => 'from',
        'currency' => 'SAR',
        'duration' => 'Duration',
        'includes' => 'Includes',
        'ask' => 'Ask about this',
        'all' => 'All services and details',
        'note' => 'Prices are in Saudi riyals and are starting points. The final quote is written and fixed before we start.',
        'items' => [
            'site' => [
                'title' => 'A professional website',
                'blurb' => 'A site for your company or yourself, in Arabic and English, with a dashboard that lets you edit everything.',
                'time' => '1 to 2 weeks',
                'includes' => ['Design for your brand', 'Arabic and English', 'Dashboard', 'Search-engine setup'],
            ],
            'platform' => [
                'title' => 'A store or booking platform',
                'blurb' => 'Bookings, orders and online payment, with one dashboard for admin and one for providers.',
                'time' => '4 to 8 weeks',
                'includes' => ['Book or order on the site', 'Online payment', 'Dashboards', 'Customer notifications'],
            ],
            'system' => [
                'title' => 'A custom management system',
                'blurb' => 'An internal system built around how you actually work: requests, stages, approvals and reports.',
                'time' => '6 to 10 weeks',
                'includes' => ['Analysis of your workflow', 'Roles and permissions', 'Reports and tracking boards', 'Migration of your current data'],
            ],
            'app' => [
                'title' => 'A mobile app',
                'blurb' => 'An app for iPhone and Android, with the backend and the dashboard that run it.',
                'time' => '8 to 12 weeks',
                'includes' => ['iPhone and Android', 'Backend and API', 'Dashboard', 'Store publishing'],
            ],
            'api' => [
                'title' => 'Integrations and APIs',
                'blurb' => 'Connecting your system to payment gateways, messaging and other systems, or building an API for your app.',
                'time' => '1 to 3 weeks',
                'includes' => ['Payment gateways', 'Messaging and notifications', 'System-to-system links', 'Full documentation'],
            ],
            'ai' => [
                'title' => 'AI automation',
                'blurb' => 'Agents that work for you: they collect data, sort requests and write your report every day.',
                'time' => '1 to 3 weeks',
                'includes' => ['An agent on a schedule', 'Data collection and sorting', 'Ready-made reports', 'Instant alerts'],
            ],
        ],
    ],

    'work' => [
        'tag' => 'Work',
        'title' => 'Every project is a world.',
        'lead' => 'No single template fits everyone. These are real projects running now, each built to its own measure.',
        'open' => 'Open',
        'visit' => 'Visit the site',
        'story' => 'Project story',
        'all' => 'All work',
        'role' => 'What I built',
        'next' => 'Next project',
        'confidential' => 'Government project, no screenshots shown.',
    ],

    'worlds' => [
        'story' => 'Project story',
        'visit' => 'Visit the site',
        'built' => 'What I built',
        'sajjah' => [
            'line' => 'A diwaniya night starts with a booking.',
        ],
        'shattib' => [
            'line' => 'From the drawing to the key.',
        ],
        'rouaa' => [
            'line' => 'A site its owner re-colours in one click.',
            'hint' => 'Try it: pick a colour',
        ],
        'nour' => [
            'line' => 'A standard you can see, not just read about.',
        ],
        'archive' => [
            'tag' => 'Before that',
            'title' => 'Earlier work',
        ],
    ],

    'ai' => [
        'tag' => 'How I work',
        'title' => 'I work with AI. The responsibility *stays mine*.',
        'lead' => 'I run a team of AI agents working in parallel: one researches, one writes the tests, one reviews the code. You get faster delivery at a lower cost, and every line passes through me before it reaches you.',
        'agents' => ['Research', 'Tests', 'Code review', 'Docs'],
        'points' => [
            ['title' => 'Faster', 'text' => 'Repetitive work is done in hours instead of days, so my time goes to what actually needs thinking.'],
            ['title' => 'More precise', 'text' => 'Several automated code reviews run before mine. On one project they lifted the code-quality score from 23 to 82 out of 100.'],
            ['title' => 'Works while you sleep', 'text' => 'I build agents that collect data from several sources every day and turn it into a report on your desk each morning.'],
        ],
    ],

    'process' => [
        'tag' => 'How we work',
        'title' => 'Four steps, *no* surprises.',
        'steps' => [
            ['title' => 'We talk', 'text' => 'A free call where I learn your business and what you really need, not what it looks like you need.'],
            ['title' => 'I write you a quote', 'text' => 'A clear scope, a fixed price and a timeline. You approve it before anything starts.'],
            ['title' => 'I build, you watch', 'text' => 'A preview you follow week by week, changing course along the way instead of at the end.'],
            ['title' => 'We launch, I stay', 'text' => 'Launch, then dashboard training, then follow-up once it is running.'],
        ],
    ],

    'faq' => [
        'tag' => 'Asked often',
        'title' => 'Before you *ask*.',
        'items' => [
            ['q' => 'How much will my project cost?', 'a' => 'It depends on its size. A professional website starts from :site_from SAR, a store or booking platform from :platform_from SAR, a custom management system from :system_from SAR, and a mobile app from :app_from SAR. Those are starting points; after a short call I send you a written, fixed-price quote.'],
            ['q' => 'How long does it take?', 'a' => 'A professional website takes one to two weeks. Systems and platforms take one to three months depending on size. The timeline is written in the quote and I keep to it.'],
            ['q' => 'Do I own the code and the site after delivery?', 'a' => 'Yes. The code, the domain and the hosting are all in your name, and you receive everything at delivery.'],
            ['q' => 'Do you work with clients outside Riyadh?', 'a' => 'Yes. I am based in Riyadh and work remotely with clients across Saudi Arabia and abroad.'],
            ['q' => 'What if I need changes after launch?', 'a' => 'I stay available. Small changes are done quickly, and larger additions are agreed as a new phase at a clear price.'],
            ['q' => 'Do you use AI on my project?', 'a' => 'Yes, to speed the work up and review it. But every decision and every line passes through me, and the result is my responsibility alone.'],
        ],
    ],

    'cta' => [
        'tag' => 'Next step',
        'l1' => 'Got an idea?',
        'alt' => 'Let’s',
        'l2' => 'build it.',
        'lead' => 'Tell me your idea in one message. I reply within one business day, and the first call is free.',
        'whatsapp' => 'WhatsApp',
        'form' => 'Write to me',
    ],

    'footer' => [
        'local' => 'Riyadh now',
        'tagline' => 'Freelance software engineer in Riyadh. I build websites, systems and apps for clients across Saudi Arabia.',
        'pages' => 'Pages',
        'reach' => 'Reach me',
        'rights' => 'All rights reserved.',
        'top' => 'Back to top',
        'made' => 'Built dot by dot.',
    ],

    'estimate' => [
        'tag' => 'Project calculator',
        'title' => 'Price your project in a minute.',
        'lead' => 'Pick what you need and you will see an approximate price range and timeline. Then send it to me in one tap.',
        'step_type' => 'What do you want to build?',
        'step_extras' => 'What else does it need?',
        'step_pace' => 'When do you need it?',
        'extras' => [
            'bilingual' => 'Arabic and English',
            'payments' => 'Online payment',
            'dashboard' => 'Advanced dashboard',
            'integrations' => 'Links to other systems',
            'ai' => 'AI features',
            'content' => 'Content writing',
        ],
        'pace' => [
            'normal' => 'Normal pace',
            'fast' => 'Urgent',
            'flex' => 'No rush',
        ],
        'result' => 'Approximate cost',
        'to' => 'to',
        'time' => 'Expected duration',
        'weeks' => 'weeks',
        'disclaimer' => 'A first, non-binding estimate. The final price follows a short call.',
        'send' => 'Send the estimate on WhatsApp',
        'message_intro' => 'Hi Baraa, I priced my project on your site:',
        'message_type' => 'Type',
        'message_extras' => 'Extras',
        'message_pace' => 'Pace',
        'message_range' => 'Estimate',
        'none' => 'No extras',
    ],

    'pages' => [
        'services' => [
            'tag' => 'Services & prices',
            'title' => 'What do you want to build?',
            'lead' => 'Six kinds of projects, each with a starting price and a clear timeline. If yours is not here, price it in the calculator or write to me.',
            'mark' => '?',
            'ask_prefix' => 'Hi Baraa, I would like to ask about:',
        ],
        'work' => [
            'tag' => 'Work',
            'title' => 'What I have built so far.',
            'lead' => 'Two booking and service platforms, sites for independent professionals, and earlier work. Each project has its story: the challenge, the solution, the result.',
            'earlier' => 'Earlier work',
            'kinds' => ['gov' => 'Government platform', 'product' => 'Product', 'client' => 'Client project', 'lab' => 'Earlier project'],
        ],
        'project' => [
            'back' => 'All work',
            'challenge' => 'The challenge',
            'solution' => 'What I built',
            'result' => 'The result',
            'role' => 'My role',
            'year' => 'Year',
            'sector' => 'Sector',
            'similar' => 'Want something similar?',
            'similar_cta' => 'Let us talk about it',
        ],
        'about' => [
            'short' => 'In short',
            'tag' => 'About',
            'experience' => 'Where I have worked',
            'can' => 'What I can build for you',
            'mark' => 'Hello',
        ],
        'contact' => [
            'tag' => 'Contact',
            'mark' => 'Hello',
            'or' => 'Or write to me here',
            'fastest' => 'Fastest',
        ],
        'missing' => [
            'title' => 'This page does not exist.',
            'lead' => 'The link may have changed or been mistyped. Every page is in the menu, and the services and work are right here.',
        ],
        'blog' => [
            'title' => 'The blog is being written.',
            'lead' => 'Practical articles on building websites and systems, soon.',
        ],
    ],
];
