@php
    $contact = \App\Support\SiteSettings::forFrontend(app()->getLocale())['contact'];
    $brand = \App\Support\SiteSettings::brandName();
    $anchor = \App\Support\Seo::locationAnchor();
    $map = \App\Support\Seo::mapUrl();
@endphp

{{--
    The location section, as delivered rather than as rendered.

    The site is client-rendered: the body React fills in is empty in the HTML
    that leaves the server, so a crawler that does not run JavaScript — and
    every link preview, every feed reader — sees a page with no address on it.
    This is that same address, verbatim, in the response itself.

    It is the content the section renders, not a second version of it: the
    values come from the settings the React component reads, so the two cannot
    say different things. Anything else here would be cloaking.
--}}
<section id="{{ $anchor }}-fallback" aria-labelledby="{{ $anchor }}-fallback-title">
    <h2 id="{{ $anchor }}-fallback-title">{{ __('site.about.location_title') }}</h2>
    <p>{{ __('site.about.location_lead') }}</p>

    <address>
        <strong>{{ $brand }}</strong><br>
        {{ $contact['address'] }}<br>
        {{ $contact['district'] }}, {{ $contact['city'] }}<br>
        {{ $contact['postal_box'] }}<br>
        {{ $contact['region'] }}, {{ $contact['country'] }}
    </address>

    <p>
        <strong>{{ __('site.contact.hours') }} :</strong>
        {{ $contact['hours'] }}
    </p>

    <p>
        <a href="tel:{{ preg_replace('/\s+/', '', (string) $contact['phone']) }}">{{ $contact['phone'] }}</a>
        &middot;
        <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>
    </p>

    @if ($map)
        <p>
            <a href="{{ $map }}" rel="noreferrer">
                {{ __('site.contact.find_us') }} — {{ $contact['district'] }}, {{ $contact['city'] }}
            </a>
        </p>
    @endif
</section>
