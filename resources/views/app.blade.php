@php
    $seo = $seo ?? \App\Support\Seo::get();
    $brand = \App\Support\SiteSettings::brandName();
    $locale = app()->getLocale();
    $alternates = \App\Support\LocalePath::alternates(request()->path());
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b090b">

    {{-- Crawler-facing metadata is rendered here rather than through Inertia's
         <Head>, so it is present without running a separate SSR process. --}}
    <title>{{ $seo['full_title'] ?? $brand }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    @if (! empty($seo['noindex']))
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow, max-image-preview:large">
    @endif

    <meta property="og:site_name" content="{{ $brand }}">
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:title" content="{{ $seo['full_title'] ?? $brand }}">
    <meta property="og:description" content="{{ $seo['description'] ?? '' }}">
    <meta property="og:url" content="{{ $seo['canonical'] ?? url()->current() }}">
    <meta property="og:locale" content="{{ $locale === 'fr' ? 'fr_CM' : 'en_GB' }}">
    @if (! empty($seo['image']))
        <meta property="og:image" content="{{ str_starts_with($seo['image'], 'http') ? $seo['image'] : url($seo['image']) }}">
    @endif
    @if (! empty($seo['published_at']))
        <meta property="article:published_time" content="{{ $seo['published_at'] }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ config('site.seo.twitter') }}">

    {{-- Search-console ownership. The meta-tag proof rather than the uploaded
         file: a deploy replaces public/, and a verification that disappears
         with a build is a property that silently unverifies itself. Emitted
         only when a token is set — an empty content attribute reads to the
         verifier as a failed proof rather than an absent one. --}}
    @if ($google = config('site.seo.verification.google'))
        <meta name="google-site-verification" content="{{ $google }}">
    @endif
    @if ($bing = config('site.seo.verification.bing'))
        <meta name="msvalidate.01" content="{{ $bing }}">
    @endif

    {{-- Each language now has its own address, so these point at the real
         counterpart rather than all three naming the current URL. --}}
    @foreach ($alternates as $code => $href)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $alternates[config('site.default_locale')] }}">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @if (! empty($seo['preload']))
        @php($lcp = $seo['preload'])
        @if (preg_match('/-1280\.webp$/', $lcp))
            <link rel="preload" as="image" href="{{ $lcp }}" imagesrcset="{{ collect([320, 640, 960, 1280, 1920])->map(fn ($w) => preg_replace('/-1280\.webp$/', "-{$w}.webp", $lcp)." {$w}w")->implode(', ') }}" imagesizes="{{ $seo['preload_sizes'] ?? '100vw' }}" fetchpriority="high">
        @else
            <link rel="preload" as="image" href="{{ $lcp }}" fetchpriority="high">
        @endif
    @endif
    <link rel="apple-touch-icon" href="/favicon.svg">

    {{-- Theme applied before first paint: no flash between dark and light (THE-01). --}}
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        (function () {
            var t = 'dark';
            try { t = localStorage.getItem('feirry-theme') || 'dark'; } catch (e) {}
            if (t !== 'light') t = 'dark';
            document.documentElement.dataset.theme = t;
            if (t === 'light') document.querySelector('meta[name="theme-color"]').setAttribute('content', '#faf7f2');
        })();
    </script>

    <link rel="preload" href="/fonts/clash/ClashDisplay-Bold.woff2" as="font" type="font/woff2" crossorigin>

    <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        {!! json_encode($seo['schema'] ?? \App\Support\Seo::organizationSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
    </script>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="antialiased v7-body">
    {{-- Everything React renders is absent from the response itself, so a
         reader that does not execute JavaScript gets an empty page. Pages that
         name a partial through Seo::set(['noscript' => ...]) put their content
         here, in the HTML as delivered. It is the same content the page
         renders, from the same source — not a second version of it. --}}
    <noscript>
        @isset($seo['noscript'])
            @include($seo['noscript'])
        @endisset

        <div style="padding:2rem;font-family:sans-serif;text-align:center">
            Ce site a besoin de JavaScript pour fonctionner. / This site requires JavaScript.
        </div>
    </noscript>
    @inertia
</body>
</html>
