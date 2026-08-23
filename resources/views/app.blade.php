@php
    $seo = $seo ?? \App\Support\Seo::get();
    $brand = \App\Support\SiteSettings::brandName();
    $locale = app()->getLocale();
    $alternates = \App\Support\LocalePath::alternates(request()->path());
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
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

    {{-- Each language now has its own address, so these point at the real
         counterpart rather than all three naming the current URL. --}}
    @foreach ($alternates as $code => $href)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $alternates[config('site.default_locale')] }}">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon.svg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700;1,800&family=Manrope:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script type="application/ld+json">
        {!! json_encode($seo['schema'] ?? \App\Support\Seo::organizationSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @routes
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="antialiased">
    <noscript>
        <div style="padding:2rem;font-family:sans-serif;text-align:center">
            Ce site a besoin de JavaScript pour fonctionner. / This site requires JavaScript.
        </div>
    </noscript>
    @inertia
</body>
</html>
