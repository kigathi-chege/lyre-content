<?php

namespace Lyre\Content\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Lyre\Facet\Filament\Plugins\LyreFacetFilamentPlugin;
use Lyre\File\Filament\Plugins\LyreFileFilamentPlugin;

class LyreContentFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'lyre.content';
    }

    public function register(Panel $panel): void
    {
        $resources = get_filament_resources_for_namespace('Lyre\\Content\\Filament\\Resources');
        $panel
            ->resources($resources)
            ->plugins([
                new LyreFileFilamentPlugin(),
                new LyreFacetFilamentPlugin(),
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
