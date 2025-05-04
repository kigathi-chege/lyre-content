<?php

namespace Lyre\Content\Filament\Resources\SectionTextResource\Pages;

use Lyre\Content\Filament\Resources\SectionTextResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSectionTexts extends ListRecords
{
    protected static string $resource = SectionTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
