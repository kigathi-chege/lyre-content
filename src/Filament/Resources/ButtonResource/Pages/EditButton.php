<?php

namespace Lyre\Content\Filament\Resources\ButtonResource\Pages;

use Lyre\Content\Filament\Resources\ButtonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditButton extends EditRecord
{
    protected static string $resource = ButtonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
