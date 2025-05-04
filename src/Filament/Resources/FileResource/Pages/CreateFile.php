<?php

namespace Lyre\Content\Filament\Resources\FileResource\Pages;

use Lyre\Content\Filament\Resources\FileResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;

class CreateFile extends CreateRecord
{
    protected static string $resource = FileResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $absolutePath = storage_path('app/public/' . $data['file']);

        $uploadedFile = new UploadedFile(
            $absolutePath,
            basename($absolutePath),
            mime_content_type($absolutePath),
            null,
            true
        );

        $fileRepository = app(\Lyre\Content\Repositories\Contracts\FileRepositoryInterface::class);
        $record = $fileRepository->uploadFile($uploadedFile, $data['name'] ?? null, $data['description'] ?? null);

        unlink($absolutePath);

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }
}
