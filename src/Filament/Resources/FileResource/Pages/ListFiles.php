<?php

namespace Lyre\Content\Filament\Resources\FileResource\Pages;

use Lyre\Content\Filament\Resources\FileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFiles extends ListRecords
{
    protected static string $resource = FileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
