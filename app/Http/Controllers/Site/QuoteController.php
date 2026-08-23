<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteRequest;
use App\Models\Faq;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuoteController extends Controller
{
    public function index(): Response
    {
        Seo::set([
            'title' => __('site.quote.title'),
            'description' => __('site.quote.hero_lead'),
        ]);

        return Inertia::render('Quote', [
            'services' => Service::published()->get(['id', 'slug', 'title_fr', 'title_en', 'icon'])
                ->map(fn (Service $s) => [
                    'id' => $s->id,
                    'slug' => $s->slug,
                    'title' => $s->tr('title'),
                    'icon' => $s->icon,
                ]),
            'faqs' => Faq::published()->where('group', 'quote')->get()->map->toArrayLocalised(),
        ]);
    }

    public function store(StoreQuoteRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('attachment', 'website');

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('quotes', 'public');
        }

        $quote = QuoteRequest::create($data);

        return back()->with([
            'success' => __('site.quote.success', ['reference' => $quote->reference]),
            'reference' => $quote->reference,
        ]);
    }
}
