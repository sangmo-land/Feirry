<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Support\LocalePath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Kept for links published before English had its own addresses.
     *
     * The switcher no longer comes through here — it points straight at the
     * equivalent URL, so the choice is a real, shareable link rather than a
     * session flag. This forwards anything still holding the old address to
     * wherever the visitor was, in the language asked for.
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('site.locales')), 404);

        $from = $request->headers->get('referer');
        $path = $from ? parse_url($from, PHP_URL_PATH) ?: '/' : '/';

        return redirect(LocalePath::to($path, $locale));
    }
}
