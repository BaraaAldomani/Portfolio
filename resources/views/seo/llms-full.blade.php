{{-- /llms-full.txt: the long form of /llms.txt. Services in detail with their
     own questions, the general questions, the process and the work. Plain
     text, so nothing here is HTML-escaped. --}}
# Baraa Aldomani (بَراء الدوماني), freelance software engineer in Riyadh

> Baraa Aldomani is a freelance software engineer based in Riyadh, Saudi Arabia.
> He works directly with founders and business owners to turn an idea into a
> working product: websites, stores and booking platforms, custom management
> systems, mobile apps, API integrations and AI automation. Scope and price are
> written down before work starts, and starting prices are public.

The short index of this file is [llms.txt]({!! route('llms') !!}). Everything
here is also on the public pages, in Arabic and English.

## Facts
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
@php($page = \App\Support\ServicePages::content($key, 'en'))

### {!! $page['name'] !!} ({!! $tAr("site.offers.items.$key.title") !!})
{!! $page['answer'] !!}
- Starting price: {!! number_format($price['from']) !!} SAR. Usual duration: {!! $price['weeks'][0] !!} to {!! $price['weeks'][1] !!} weeks.
- For: {!! implode(' ', $page['fit']) !!}
- Delivered: {!! implode('; ', array_column($page['gets'], 't')) !!}.
@foreach ($page['faq'] as $qa)
- {!! $qa['q'] !!} {!! $qa['a'] !!}
@endforeach
- Page: [{!! $page['name'] !!}]({!! service_url($key, 'en') !!}), Arabic: [{!! $tAr("site.offers.items.$key.title") !!}]({!! service_url($key, 'ar') !!})
@endforeach

## How a project runs
@foreach ((array) trans('site.process.steps', [], 'en') as $i => $step)
{!! $i + 1 !!}. {!! $step['title'] !!}: {!! $step['text'] !!}
@endforeach

## Work
@foreach ($projects as $project)
- [{!! $project->name_en ?: $project->title_en !!}]({!! route('projects.show', ['locale' => 'en', 'project' => $project->slug_en]) !!}) ({!! $project->name_ar ?: $project->title_ar !!}), {!! $project->sector_en !!}@if ($project->year), {!! $project->year !!}@endif: {!! $project->summary_en !!} Challenge: {!! $project->problem_en !!} Built: {!! $project->solution_en !!} Result: {!! $project->result_en !!}@if ($project->live_url) Live at {!! $project->live_url !!}@endif

@endforeach

## Common questions
@foreach ($faq as $qa)
- {!! $qa['q'] !!} {!! $qa['a'] !!}
@endforeach

## Contact
- [WhatsApp](https://wa.me/{!! $whatsapp !!}): the fastest way to reach him
- [Email](mailto:{!! $email !!}): {!! $email !!}
- [Contact page]({!! route('contact', ['locale' => 'en']) !!}): a short form, answered within one business day
@foreach ($profiles as $label => $url)
- [{!! $label !!}]({!! $url !!})
@endforeach

## Who he is a fit for
Someone in Saudi Arabia (or working with the Saudi market) who needs a website,
an online store or booking platform, a custom internal system, a mobile app, a
payment or API integration, or an AI automation, and wants one accountable
engineer with a written scope and a fixed price. The first step is a free call,
by WhatsApp or through the contact page.
