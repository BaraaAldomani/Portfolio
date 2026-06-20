<?php

namespace App\Http\Controllers;

use App\Support\Seo\PageMeta;
use Illuminate\View\View;

final class BlogController
{
    public function index(): View
    {
        return view('pages.stub', [
            'meta' => PageMeta::fromLang('blog'),
            'heading' => __('nav.blog'),
        ]);
    }
}
