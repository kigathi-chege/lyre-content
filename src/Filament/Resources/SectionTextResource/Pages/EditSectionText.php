<?php

namespace Lyre\Content\Filament\Resources\SectionTextResource\Pages;

use Lyre\Content\Filament\Resources\SectionTextResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSectionText extends EditRecord
{
    protected static string $resource = SectionTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
