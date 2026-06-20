<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// SEO endpoints (locale-agnostic, served at the root).
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [SitemapController::class, 'llms'])->name('llms');

// Bare root → default locale (Arabic-first).
Route::redirect('/', '/ar', 301);

// Old version-prefixed links (/v1|/v2|/v3/...) → their version-less equivalent,
// so existing bookmarks and indexed URLs keep working now that v3 is the site.
Route::get('/{version}/{locale}/{rest?}', function (string $version, string $locale, ?string $rest = null) {
    return redirect('/' . $locale . ($rest ? '/' . $rest : ''), 301);
})->whereIn('version', ['v1', 'v2', 'v3'])
    ->whereIn('locale', config('app.supported_locales'))
    ->where('rest', '.*');

// The site, served per locale.
Route::prefix('{locale}')
    ->whereIn('locale', config('app.supported_locales'))
    ->middleware([SetLocale::class])
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/services', ServicesController::class)->name('services');
        Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectsController::class, 'show'])->name('projects.show');
        Route::get('/about', AboutController::class)->name('about');
        Route::get('/contact', [ContactController::class, 'show'])->name('contact');
        Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
        Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    });
