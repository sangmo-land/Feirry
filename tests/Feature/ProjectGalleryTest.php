<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A project uploaded with a cover and six pictures must show the six pictures.
 * The page used to cap the gallery at four small tiles, and the list preview
 * carried the cover only (22/11/2026).
 */
class ProjectGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function projectWithSixPictures(): Project
    {
        $project = Project::published()->firstOrFail();
        $project->update(['gallery' => collect(range(1, 6))->map(fn ($i) => "projects/gallery/photo-{$i}.png")->all()]);

        return $project;
    }

    public function test_the_project_page_receives_every_uploaded_picture(): void
    {
        $project = $this->projectWithSixPictures();

        $this->get('/projets/'.$project->slug)->assertOk()->assertInertia(fn ($page) => $page
            ->has('project.gallery', 6)
            ->where('project.gallery.0', '/storage/projects/gallery/photo-1.png')
            ->where('project.gallery.5', '/storage/projects/gallery/photo-6.png'));
    }

    public function test_the_project_list_carries_the_pictures_for_its_preview(): void
    {
        $project = $this->projectWithSixPictures();

        $this->get('/projets')->assertOk()->assertInertia(function ($page) use ($project) {
            $card = collect($page->toArray()['props']['projects'])->firstWhere('slug', $project->slug);
            $this->assertCount(6, $card['gallery']);
            $this->assertSame('/storage/projects/gallery/photo-3.png', $card['gallery'][2]);
        });
    }

    public function test_a_project_without_pictures_still_renders(): void
    {
        $project = Project::published()->firstOrFail();
        $project->update(['gallery' => null]);

        $this->get('/projets/'.$project->slug)->assertOk()->assertInertia(fn ($page) => $page->has('project.gallery', 0));
        $this->get('/projets')->assertOk();
    }

    public function test_the_page_script_no_longer_caps_the_gallery_at_four(): void
    {
        $source = file_get_contents(resource_path('js/Pages/V7/Projet.jsx'));

        $this->assertStringNotContainsString('.slice(0, 4)', $source);
        $this->assertStringContainsString('GalleryGrid', $source);
    }
}
