<?php

namespace Lyre\Content\Console\Commands;

use Illuminate\Console\Command;

class GenerateFilamentResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament:resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command should create all the resources for Filament';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->generateFilamentResources();
    }

    public function getModelClasses($directory = null, &$modelClasses = [])
    {
        if (is_null($directory)) {
            $directory = app_path("Models");
        }

        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'BaseModel.php') {
                continue;
            }

            $filePath = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filePath)) {
                $this->getModelClasses($filePath, $modelClasses);
            } else {
                $modelName = str_replace('.php', '', $file);
                $relativePath = str_replace(app_path() . DIRECTORY_SEPARATOR, '', $directory);
                $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
                $modelClass = "App\\$namespace\\$modelName";
                $modelClasses[$modelName] = $modelClass;
            }
        }

        return $modelClasses;
    }

    public function filamentResourceExists($resourcePath)
    {
        $resourceFile = app_path("Filament/Resources/{$resourcePath}Resource.php");
        return file_exists($resourceFile);
    }

    public function generateFilamentResources()
    {
        $modelClasses = $this->getModelClasses();

        foreach ($modelClasses as $modelName => $resourcePath) {
            $resourcePath = str_replace('App\\Models\\', '', $resourcePath);
            $resourcePath = str_replace('\\', '/', $resourcePath);
            echo "Generating Filament resource for: {$resourcePath}\n";
            if (!$this->filamentResourceExists($resourcePath)) {
                try {
                    \Illuminate\Support\Facades\Artisan::call('make:filament-resource', [
                        'name' => $resourcePath,
                        '--generate' => true,
                        /**
                     * TODO: Kigathi - June 13 2024 - Extend filament to accept resource namespace param
                     *  '--resource-namespace' => 'App\\Filament\\Resources'
                     *
                     * Quick fix:
                     *
                     * Go to:
                     * /vendor/filament/filament/src/Commands/MakeResourceCommand.php
                     *
                     * Comment out:
                     * $namespace = (!$resourceNamespace && count($resourceNamespaces) > 1) ?
                     *       select(
                     *           label : 'Which namespace would you like to create this in?',
                     *           options: $resourceNamespaces
                     *       ):
                     *       (Arr::first($resourceNamespaces) ?? 'App\\Filament\\Resources');
                     *
                     * Add:
                     * $namespace = 'App\\Filament\\Resources';
                     */
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                echo "Generated Filament resource for: {$resourcePath}\n";
            } else {
                echo "Filament resource already exists for: {$resourcePath}\n";
            }
        }
    }
}
