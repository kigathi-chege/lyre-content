<?php

namespace Lyre\Content\Providers;

use Illuminate\Support\ServiceProvider;
use Lyre\Content\Console\Commands\GenerateFilamentResources;
use Livewire\Livewire;
use Lyre\Content\Filament\Resources\PageResource\RelationManagers\SectionsRelationManager;
use Lyre\Content\Filament\Resources\SectionResource\RelationManagers\DataRelationManager;

class LyreContentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        register_repositories($this->app, 'Lyre\\Content\\Repositories', 'Lyre\\Content\\Repositories\\Contracts');

        $this->commands([GenerateFilamentResources::class]);
    }

    public function boot(): void
    {
        register_global_observers("Lyre\\Content\\Models");

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ]);

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        Livewire::component('lyre.content.filament.relation-managers.sections-relation-manager', SectionsRelationManager::class);
        Livewire::component('lyre.content.filament.relation-managers.data-relation-manager', DataRelationManager::class);
    }
}
