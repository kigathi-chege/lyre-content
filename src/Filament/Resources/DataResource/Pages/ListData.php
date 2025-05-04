<?php

namespace Lyre\Content\Filament\Resources\DataResource\Pages;

use Lyre\Content\Filament\Resources\DataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListData extends ListRecords
{
    protected static string $resource = DataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
