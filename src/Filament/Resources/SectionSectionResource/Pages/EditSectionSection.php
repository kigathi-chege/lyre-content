<?php

namespace Lyre\Content\Filament\Resources\SectionSectionResource\Pages;

use Lyre\Content\Filament\Resources\SectionSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSectionSection extends EditRecord
{
    protected static string $resource = SectionSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
