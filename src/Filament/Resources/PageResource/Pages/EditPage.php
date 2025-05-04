<?php

namespace Lyre\Content\Filament\Resources\PageResource\Pages;

use Lyre\Content\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    // TODO: Kigathi - April 23 2025 - It is possible to delete pages with this knowledge:
    // https://svelte.dev/docs/kit/advanced-routing
    // But for now, we must not delete pages.
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }
}
