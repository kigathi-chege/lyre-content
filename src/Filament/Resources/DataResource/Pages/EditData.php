<?php

namespace Lyre\Content\Filament\Resources\DataResource\Pages;

use Lyre\Content\Filament\Resources\DataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditData extends EditRecord
{
    protected static string $resource = DataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
