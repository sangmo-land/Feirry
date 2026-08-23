<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Models\JobOpening;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CareerController extends Controller
{
    public function index(): Response
    {
        Seo::set([
            'title' => __('site.careers.title'),
            'description' => __('site.careers.hero_lead'),
        ]);

        $openings = JobOpening::published()->get();

        return Inertia::render('Careers/Index', [
            'openings' => $openings->map->toCard(),
            'departments' => $openings->pluck('department')->filter()->unique()->values(),
        ]);
    }

    public function show(JobOpening $jobOpening): Response
    {
        abort_unless($jobOpening->is_published, 404);

        Seo::set([
            'title' => $jobOpening->tr('title'),
            'description' => str($jobOpening->tr('description'))->stripTags()->limit(170)->value(),
            'type' => 'article',
        ]);

        return Inertia::render('Careers/Show', [
            'opening' => $jobOpening->toDetail(),
            'others' => JobOpening::published()->whereKeyNot($jobOpening->id)->limit(4)->get()->map->toCard(),
        ]);
    }

    public function apply(StoreJobApplicationRequest $request, JobOpening $jobOpening): RedirectResponse
    {
        abort_unless($jobOpening->is_published, 404);

        $jobOpening->applications()->create(
            $request->safe()->except('cv', 'website') + [
                'cv_path' => $request->file('cv')->store('applications', 'local'),
            ]
        );

        return back()->with('success', __('site.careers.success'));
    }
}
