<?php

namespace Lyre\Content\Filament\Resources\FileResource\Pages;

use Lyre\Content\Filament\Resources\FileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditFile extends EditRecord
{
    protected static string $resource = FileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function ($record, $action) {

                    $record->attachments()->delete();

                    $result = $action->process(static fn(Model $record) => $record->delete());

                    if (! $result) {
                        $action->failure();

                        return;
                    }

                    unlink(storage_path('app/private/' . $record->path));
                    unlink(storage_path('app/private/' . $record->path_sm));
                    unlink(storage_path('app/private/' . $record->path_md));
                    unlink(storage_path('app/private/' . $record->path_lg));

                    $action->success();
                }),
        ];
    }
}
