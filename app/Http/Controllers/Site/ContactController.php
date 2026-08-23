<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        Seo::set([
            'title' => __('site.contact.title'),
            'description' => __('site.contact.hero_lead'),
        ]);

        return Inertia::render('Contact', [
            'faqs' => Faq::published()->where('group', 'general')->limit(6)->get()->map->toArrayLocalised(),
        ]);
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        ContactMessage::create($request->safe()->except('website') + [
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', __('site.contact.success'));
    }
}
