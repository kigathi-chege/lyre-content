<?php

namespace Lyre\Content\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class SelectFromGallery extends Field
{
    protected bool | Closure $multiple = false;

    protected array $galleryFiles = [];
    protected array $selectedFiles = [];
    protected int $galleryPage = 1;
    protected int $perPage = 8;

    protected string $view = 'forms.components.select-from-gallery';

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        $static->galleryFiles();

        return $static;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (SelectFromGallery $component, $state) {
            $record = $component->getRecord();

            if ($record) {
                $component->selectedFiles = $record->files->toArray();
            }
        });
    }

    public function multiple(bool| Closure  $condition = true): static
    {
        $this->multiple = $condition;
        return $this;
    }

    public function getMultiple($sth = false): ?bool
    {
        return $this->evaluate($this->multiple);
    }

    public function galleryFiles(int $perPage = 8, int $page = 1): static
    {
        $page = $this->galleryPage ?? 1;
        $fileRepository = app(\Lyre\Content\Repositories\Contracts\FileRepositoryInterface::class);
        $this->galleryFiles = $fileRepository->paginate($this->perPage, $page)->all();

        return $this;
    }

    public function getGalleryFiles(): ?array
    {
        return $this->evaluate($this->galleryFiles);
    }

    public function selectedFiles($files): static
    {
        $this->selectedFiles = $files;

        return $this;
    }

    public function getSelectedFiles(): ?array
    {
        return $this->evaluate($this->selectedFiles);
    }

    public function getGalleryFilesJson(): ?string
    {
        $galleryFiles = $this->evaluate($this->galleryFiles);
        return $galleryFiles ? json_encode($galleryFiles) : null;
    }

    public function galleryPage(int $page): static
    {
        $this->galleryPage = $page;
        return $this;
    }

    public function getGalleryPage(): ?int
    {
        return $this->evaluate($this->galleryPage);
    }
}
