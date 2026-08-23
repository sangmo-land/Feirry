<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class LegalController extends Controller
{
    public function notice(): Response
    {
        Seo::set(['title' => __('site.footer.legal'), 'noindex' => true]);

        return Inertia::render('Legal', ['document' => 'notice']);
    }

    public function privacy(): Response
    {
        Seo::set(['title' => __('site.footer.privacy'), 'noindex' => true]);

        return Inertia::render('Legal', ['document' => 'privacy']);
    }
}
