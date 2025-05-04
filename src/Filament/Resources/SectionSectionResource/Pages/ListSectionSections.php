<?php

namespace Lyre\Content\Filament\Resources\SectionSectionResource\Pages;

use Lyre\Content\Filament\Resources\SectionSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSectionSections extends ListRecords
{
    protected static string $resource = SectionSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
