<?php

namespace Lyre\Content\Filament\Resources\PageResource\Pages;

use Lyre\Content\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    // TODO: Kigathi - April 23 2025 - It is possible to add new pages with this knowledge:
    // https://svelte.dev/docs/kit/advanced-routing
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
