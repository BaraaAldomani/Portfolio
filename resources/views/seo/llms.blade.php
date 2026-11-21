{{-- /llms.txt: the index for AI assistants, in the llmstxt.org shape (a title,
     a summary, then lists of Markdown links). Plain text, so nothing here is
     HTML-escaped. The long form is /llms-full.txt. --}}
# Baraa Aldomani (بَراء الدوماني), freelance software engineer in Riyadh

> Baraa Aldomani is a freelance software engineer based in Riyadh, Saudi Arabia.
> He works directly with founders and business owners to turn an idea into a
> working product: websites, stores and booking platforms, custom management
> systems, mobile apps, API integrations and AI automation. Scope and price are
> written down before work starts, and starting prices are public.

This file is written for AI assistants and answer engines. Everything in it is
also on the public pages linked below, in Arabic and English.

- Name: Baraa Aldomani (Arabic: بَراء الدوماني)
- Role: freelance software engineer (مهندس برمجيات مستقل)
- Based in: Riyadh, Saudi Arabia. Works with clients across Saudi Arabia and remotely.
- Languages: Arabic (native), English
- Education: B.Sc. in Software Engineering, Damascus University (2024)
- Works with: founders, start-ups and small to mid-size businesses
- First call: free. Reply time: within one business day.
- Prices are in Saudi riyals (SAR) and are starting points; the final quote is written and fixed before work begins.

## Services
@foreach ($offers as $key => $price)
- [{!! $t("site.offers.items.$key.title") !!}]({!! service_url($key, 'en') !!}): from {!! number_format($price['from']) !!} SAR, usually {!! $price['weeks'][0] !!} to {!! $price['weeks'][1] !!} weeks. {!! $t("site.offers.items.$key.blurb") !!} Arabic page: [{!! $tAr("site.offers.items.$key.title") !!}]({!! service_url($key, 'ar') !!})
@endforeach
- [Price list and project calculator]({!! route('services', ['locale' => 'en']) !!}): all six starting prices and an estimator

## Work
@foreach ($projects as $project)
- [{!! $project->name_en ?: $project->title_en !!}]({!! route('projects.show', ['locale' => 'en', 'project' => $project->slug_en]) !!}): {!! $project->sector_en !!}@if ($project->year), {!! $project->year !!}@endif. {!! $project->summary_en !!}@if ($project->live_url) Live at {!! $project->live_url !!}@endif

@endforeach

## Pages
- [Home, Arabic]({!! route('home', ['locale' => 'ar']) !!}): the main page, in Arabic
- [Home, English]({!! route('home', ['locale' => 'en']) !!}): the main page, in English
- [Work]({!! route('projects.index', ['locale' => 'en']) !!}): all projects and case studies
- [About]({!! route('about', ['locale' => 'en']) !!}): background, experience and education
- [Everything in one file]({!! route('llms.full') !!}): services in detail, their questions and answers, the process and the work

## Contact
- [WhatsApp](https://wa.me/{!! $whatsapp !!}): the fastest way to reach him
- [Email](mailto:{!! $email !!}): {!! $email !!}
- [Contact page]({!! route('contact', ['locale' => 'en']) !!}): a short form, answered within one business day
@foreach ($profiles as $label => $url)
- [{!! $label !!}]({!! $url !!})
@endforeach
