<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'name' => ['nullable', 'string', 'max:120'],
            'website' => ['prohibited'],
        ]);

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($data['email'])],
            [
                'name' => $data['name'] ?? null,
                'locale' => app()->getLocale(),
                'is_active' => true,
                'unsubscribed_at' => null,
            ],
        );

        return back()->with('success', __('site.blog.newsletter_success'));
    }
}
