<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * One address for the whole site.
 *
 * When config('portfolio.primary_url') is set, every link the site writes
 * (canonical, hreflang, sitemap, share tags) uses that address, and a request
 * that arrives on any other host the folder is served under (www, an older
 * domain) is answered with a permanent redirect to the same page there.
 *
 * This is what moves the site to a new domain: point the domain at this
 * folder, then change PRIMARY_URL. Left empty (local work, tests), the site
 * simply answers on the host it was asked for.
 */
final class PrimaryHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $primary = rtrim((string) config('portfolio.primary_url'), '/');
        $host = $primary !== '' ? parse_url($primary, PHP_URL_HOST) : null;

        if (! $host) {
            return $next($request);
        }

        URL::forceRootUrl($primary);
        URL::forceScheme(parse_url($primary, PHP_URL_SCHEME) ?: 'https');

        if (strcasecmp($request->getHost(), $host) !== 0) {
            $query = $request->getQueryString();

            return redirect()->away($primary . $request->getPathInfo() . ($query ? '?' . $query : ''), 301);
        }

        return $next($request);
    }
}
