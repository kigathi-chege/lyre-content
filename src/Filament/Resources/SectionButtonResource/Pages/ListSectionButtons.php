<?php

namespace Lyre\Content\Filament\Resources\SectionButtonResource\Pages;

use Lyre\Content\Filament\Resources\SectionButtonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSectionButtons extends ListRecords
{
    protected static string $resource = SectionButtonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
