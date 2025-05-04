<?php

namespace Lyre\Content\Filament\Resources\ButtonResource\Pages;

use Lyre\Content\Filament\Resources\ButtonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListButtons extends ListRecords
{
    protected static string $resource = ButtonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
