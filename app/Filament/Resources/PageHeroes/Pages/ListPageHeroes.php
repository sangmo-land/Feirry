<?php

namespace App\Filament\Resources\PageHeroes\Pages;

use App\Filament\Resources\PageHeroes\PageHeroResource;
use Filament\Resources\Pages\ListRecords;

class ListPageHeroes extends ListRecords
{
    protected static string $resource = PageHeroResource::class;

    /** No create action: the set of pages is fixed by the routes. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
