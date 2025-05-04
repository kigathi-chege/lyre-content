<?php

namespace Lyre\Content\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Symfony\Component\Finder\Finder;
use Filament\Resources\Resource;
use Lyre\File\Filament\Plugins\LyreFileFilamentPlugin;

class LyreContentFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'lyre.content';
    }

    public function register(Panel $panel): void
    {
        $resources = self::retrieveFilamentResources();

        $panel
            ->resources($resources)
            ->plugins([
                new LyreFileFilamentPlugin(),
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    private static function retrieveFilamentResources()
    {
        $resourceNamespace = 'Lyre\\Content\\Filament\\Resources';
        $resourcePath = realpath(__DIR__ . '/../Resources');

        $resources = collect((new Finder)->files()->in($resourcePath)->name('*.php'))
            ->map(function ($file) use ($resourceNamespace, $resourcePath) {
                // Get the relative path from resourcePath
                $relativePath = str_replace('.php', '', $file->getRelativePathname());

                // Convert slashes to namespace separators
                $class = $resourceNamespace . '\\' . str_replace('/', '\\', $relativePath);

                return class_exists($class) && is_subclass_of($class, Resource::class) ? $class : null;
            })
            ->filter()
            ->values()
            ->toArray();

        return $resources;
    }
}
