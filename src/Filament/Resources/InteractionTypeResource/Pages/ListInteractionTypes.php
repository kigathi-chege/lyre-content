<?php

namespace Lyre\Content\Filament\Resources\InteractionTypeResource\Pages;

use Lyre\Content\Filament\Resources\InteractionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInteractionTypes extends ListRecords
{
    protected static string $resource = InteractionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
