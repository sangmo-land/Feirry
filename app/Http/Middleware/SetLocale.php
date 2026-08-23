<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * The locale is a property of the URL, not of the visitor's session.
     *
     * The two languages now have distinct addresses — French at the root,
     * every other locale under a prefix equal to its code — so the path
     * already says which one was asked for. That is what makes an English
     * page shareable and indexable: previously both languages answered on the
     * same paths behind a session flag, and a crawler could only ever see the
     * French one.
     *
     * Read from the request rather than from the matched route because this
     * runs ahead of HandleInertiaRequests, which resolves the locale eagerly
     * when it builds the shared props.
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale(static::fromPath($request->path()));

        return $next($request);
    }

    /** The locale a path addresses, defaulting to the one that owns the root. */
    public static function fromPath(string $path): string
    {
        $default = config('site.default_locale');
        $segment = explode('/', trim($path, '/'))[0] ?? '';

        return $segment !== $default && array_key_exists($segment, config('site.locales'))
            ? $segment
            : $default;
    }
}
