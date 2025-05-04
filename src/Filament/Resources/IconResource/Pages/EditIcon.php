<?php

namespace Lyre\Content\Filament\Resources\IconResource\Pages;

use Lyre\Content\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIcon extends EditRecord
{
    protected static string $resource = IconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
