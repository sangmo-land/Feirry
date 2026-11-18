@php($en = request()->is('en') || request()->is('en/*'))
<!DOCTYPE html>
<html lang="{{ $en ? 'en' : 'fr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>FEIRRY - {{ $en ? 'Page not found' : 'Page introuvable' }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <style>
        @font-face{font-family:'Clash Display';src:url('/fonts/clash/ClashDisplay-Bold.woff2') format('woff2');font-weight:700;font-display:swap}
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0b090b;color:#f7f3ee;font-family:Manrope,system-ui,sans-serif;padding:24px;box-sizing:border-box}
        main{max-width:560px;text-align:center}
        img{height:30px}
        .n{font-family:'Clash Display',sans-serif;font-size:96px;line-height:1;color:#fe5000;margin:32px 0 8px}
        h1{font-family:'Clash Display',sans-serif;font-size:32px;margin:0 0 12px}
        p{color:#b3aaa2;line-height:1.6;margin:0 0 28px}
        nav{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
        a{display:inline-flex;align-items:center;height:48px;padding:0 22px;border-radius:999px;font-weight:700;text-decoration:none}
        .p{background:#fe5000;color:#0b090b}.g{border:1px solid rgba(247,243,238,.26);color:#f7f3ee}
        a:focus-visible{outline:3px solid #fe5000;outline-offset:3px}
    </style>
</head>
<body>
<main>
    <img src="/images/brand/feirry-logo-white.png" alt="FEIRRY Construction Designs">
    <div class="n" aria-hidden="true">404</div>
    <h1>{{ $en ? 'Page not found' : 'Page introuvable' }}</h1>
    <p>{{ $en ? 'This page does not exist or has moved.' : 'Cette page n’existe pas ou a été déplacée.' }}</p>
    <nav>
        <a class="p" href="{{ $en ? '/en' : '/' }}">{{ $en ? 'Back to home' : 'Retour à l’accueil' }}</a>
        <a class="g" href="{{ $en ? '/en/projects' : '/projets' }}">{{ $en ? 'Our projects' : 'Nos projets' }}</a>
        <a class="g" href="{{ $en ? '/en/my-project' : '/mon-projet' }}">{{ $en ? 'My project' : 'Mon projet' }}</a>
    </nav>
</main>
</body>
</html>
