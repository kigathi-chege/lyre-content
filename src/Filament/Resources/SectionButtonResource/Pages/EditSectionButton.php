<?php

namespace Lyre\Content\Filament\Resources\SectionButtonResource\Pages;

use Lyre\Content\Filament\Resources\SectionButtonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSectionButton extends EditRecord
{
    protected static string $resource = SectionButtonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
