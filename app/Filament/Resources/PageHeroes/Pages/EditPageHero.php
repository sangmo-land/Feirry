<?php

namespace App\Filament\Resources\PageHeroes\Pages;

use App\Filament\Resources\PageHeroes\PageHeroResource;
use Filament\Resources\Pages\EditRecord;

class EditPageHero extends EditRecord
{
    protected static string $resource = PageHeroResource::class;

    public function getTitle(): string
    {
        return 'En-tête — '.$this->getRecord()->label();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
