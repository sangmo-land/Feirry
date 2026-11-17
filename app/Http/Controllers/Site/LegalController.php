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
        Seo::set(['title' => app()->getLocale() === 'fr' ? 'Mentions légales' : 'Legal notice', 'noindex' => true]);

        return Inertia::render('V7/Legal', ['document' => 'notice']);
    }

    public function privacy(): Response
    {
        Seo::set(['title' => app()->getLocale() === 'fr' ? 'Politique de confidentialité' : 'Privacy policy', 'noindex' => true]);

        return Inertia::render('V7/Legal', ['document' => 'privacy']);
    }
}
