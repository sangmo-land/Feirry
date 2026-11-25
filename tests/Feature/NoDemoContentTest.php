<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The first version of the site shipped with demonstration copy: a founding
 * year, headcounts, branches and a plant depot that are not FEIRRY's. Search
 * engines read the page source as well as the page, and repeated those figures
 * as facts about the company. No public page may carry them, visible or not.
 */
class NoDemoContentTest extends TestCase
{
    use RefreshDatabase;

    private const DEMO = [
        '2009', '385', '340', 'Kribi', 'parc mat', 'climat c', 'plant depot', 'coastal climate',
        'trois ing', 'three engineers', 'quinze jours', 'douze mois', 'depuis 2019', 'since 2019',
    ];

    /** The page as a reader of its source sees it: entities and JSON escapes undone. */
    private function source(string $path): string
    {
        $html = html_entity_decode($this->get($path)->assertOk()->getContent(), ENT_QUOTES | ENT_HTML5);

        return json_decode('"'.str_replace(['\\/', '"'], ['/', '\\"'], preg_replace('/\\\\(?!u[0-9a-fA-F]{4})/', '\\\\\\\\', $html)).'"') ?? $html;
    }

    public function test_the_careers_pages_carry_no_demonstration_copy(): void
    {
        foreach (['/carrieres', '/en/careers'] as $path) {
            $source = $this->source($path);

            foreach (self::DEMO as $claim) {
                $this->assertStringNotContainsString($claim, $source, "{$path} still publishes « {$claim} »");
            }
        }
    }

    public function test_the_home_page_source_carries_no_unread_hero(): void
    {
        foreach (['/', '/en'] as $path) {
            $source = $this->source($path);

            foreach (self::DEMO as $claim) {
                $this->assertStringNotContainsString($claim, $source, "{$path} still publishes « {$claim} »");
            }

            $this->assertNull($this->get($path)->viewData('page')['props']['hero'] ?? null);
        }
    }
}
