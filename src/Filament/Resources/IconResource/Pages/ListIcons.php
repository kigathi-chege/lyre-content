<?php

namespace Lyre\Content\Filament\Resources\IconResource\Pages;

use Lyre\Content\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIcons extends ListRecords
{
    protected static string $resource = IconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
