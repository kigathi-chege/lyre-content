<?php

namespace Lyre\Content\Filament\Resources\FileResource\Pages;

use Lyre\Content\Filament\Resources\FileResource;
use Lyre\Content\Models\File;
use Filament\Resources\Pages\Page;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;


class Gallery extends Page
{
    protected static string $resource = FileResource::class;

    protected static string $view = 'filament.resources.content.file-resource.pages.gallery';

    protected function getViewData(): array
    {
        $page = request()->query('page', 1);
        \Log::info("PAGE", [$page]);

        $fileRepository = app(\Lyre\Content\Repositories\Contracts\FileRepositoryInterface::class);
        $files = $fileRepository->paginate(8)->all();

        \Log::info("FILES", [$files]);

        return [
            'files' => $files,
        ];
    }
}
