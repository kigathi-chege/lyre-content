<?php

namespace Lyre\Content\Filament\Resources\TextResource\Pages;

use Lyre\Content\Filament\Resources\TextResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditText extends EditRecord
{
    protected static string $resource = TextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
