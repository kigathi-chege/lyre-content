<?php

namespace Lyre\Content\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\File;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Lyre\Content\Console\Commands\GenerateFilamentResources;
use Lyre\Facades\Lyre;
use Lyre\Observer;
use Lyre\Services\ModelService;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Str;

class LyreContentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerRepositories($this->app);

        $this->commands([GenerateFilamentResources::class]);
    }

    public function boot(): void
    {
        $this->registerGlobalObserver();

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ]);

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'lyre.content');

        $this->publishes([
            __DIR__ . '/../resources/public' => public_path('lyre/content'),
        ]);
    }

    public function registerRepositories($app)
    {
        $repositoriesPath = dirname(__DIR__) . '/Repositories';
        $contractsPath = dirname(__DIR__) . '/Repositories/Contracts';

        if (! file_exists($repositoriesPath)) {
            File::makeDirectory($repositoriesPath);
        }

        if (! file_exists($contractsPath)) {
            File::makeDirectory($contractsPath);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($repositoriesPath)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) continue;

            $fileName = $file->getFilename();

            // TODO: Kigathi - April 21 2025 - Allow overriding the Repository.php by introducing a BaseRepository.php file at the repositoryPath
            if (!Str::endsWith($fileName, 'Repository.php') || $fileName === 'BaseRepository.php') {
                continue;
            }

            // Get relative path from the Repositories directory
            $relativePath = Str::after($file->getPathname(), $repositoriesPath . DIRECTORY_SEPARATOR);
            $namespacePath = str_replace(['/', '\\'], '\\', Str::replaceLast('.php', '', $relativePath));

            // Interface path must match the same relative structure
            $interfaceNamespace = 'Lyre\\Content\\Repositories\\Contracts\\' . $namespacePath . 'Interface';
            $implementationNamespace = 'Lyre\\Content\\Repositories\\' . $namespacePath;

            // Interface file must exist
            $interfaceFilePath = $contractsPath . DIRECTORY_SEPARATOR . Str::replaceLast('Repository.php', 'RepositoryInterface.php', $relativePath);

            if (file_exists($interfaceFilePath)) {
                $app->bind($interfaceNamespace, function ($app) use ($implementationNamespace) {
                    return $app->make($implementationNamespace);
                });
            }
        }
    }

    private static function registerGlobalObserver()
    {
        $modelsPath = dirname(__DIR__) . '/Models';
        $modelsBaseNamespace = "Lyre\\Content\\Models";

        $MODELS        = collect(get_model_classes($modelsPath, $modelsBaseNamespace));
        $observersPath = app_path("Observers");
        $baseNamespace = "App\\Observers";

        if (file_exists($observersPath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($observersPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if (
                    !$file->isFile() ||
                    $file->getExtension() !== 'php' ||
                    $file->getFilename() === 'BaseObserver.php'
                ) {
                    continue;
                }

                $relativePath = str_replace($observersPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $classPath = str_replace(
                    [DIRECTORY_SEPARATOR, '.php'],
                    ['\\', ''],
                    $relativePath
                );

                $observerClass = $baseNamespace . '\\' . $classPath;
                $observerName  = class_basename($observerClass);
                $modelName     = str_replace('Observer', '', $observerName);

                if (isset($MODELS[$modelName]) && class_exists($observerClass)) {
                    $modelClass = $MODELS[$modelName];
                    $modelClass::observe($observerClass);
                    $MODELS->forget($modelName);
                }
            }
        }

        foreach ($MODELS as $MODEL) {
            $MODEL::observe(Observer::class);
        }
    }
}
