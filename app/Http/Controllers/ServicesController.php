<?php

namespace App\Http\Controllers;

use App\Support\Seo\JsonLd;
use App\Support\Seo\PageMeta;
use App\Support\ServicePages;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class ServicesController
{
    /** The price list: all six services and the calculator. */
    public function __invoke(): View
    {
        return view('pages.services', [
            'meta' => PageMeta::fromLang('services'),
        ]);
    }

    /** One service on its own page, under the words people search for. */
    public function show(string $locale, string $service): View|RedirectResponse
    {
        $key = ServicePages::keyForSlug($service);

        if ($key === null) {
            // A slug from the other language (a link whose language was
            // switched by hand) still lands on the right page.
            foreach (config('app.supported_locales') as $other) {
                $match = $other !== $locale ? ServicePages::keyForSlug($service, $other) : null;
                if ($match !== null) {
                    return redirect(ServicePages::url($match), 301);
                }
            }

            abort(404);
        }

        $content = ServicePages::content($key);
        $url = ServicePages::url($key);

        return view('pages.service-show', [
            'meta' => PageMeta::forContent(
                $content['seo_title'],
                $content['seo_description'],
                ServicePages::paramsByLocale($key),
                [JsonLd::service($key, $url, $content), JsonLd::faqPage($content['faq'], $url)],
                name: $content['name'],
            ),
            'key' => $key,
            'c' => $content,
            'projects' => ServicePages::projects($key),
        ]);
    }
}
