<?php

namespace Backfron\LaravelFinder\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Console\GeneratorCommand;

class MakeFilterCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filter {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new filter.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/filter.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Finders';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $subfolder = Str::plural(class_basename($this->getNamespaceModel($name)));

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')) . "\\{$subfolder}\\Filters\\" . $name
        );
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $namespaceModel = $this->getNamespaceModel(class_basename($name));
        $model = class_basename($namespaceModel);
        $databaseField = Str::snake(class_basename($name));
        $phpVariable = Str::camel(Str::snake(class_basename($name)));

        $replace = [
            'NamespacedDummyModel' => $namespaceModel,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            'DummyDatabaseField' => $databaseField,
            '{{ dataBaseField }}' => $databaseField,
            '{{dataBaseField}}' => $databaseField,
            'DummyPhpVariable' => $phpVariable,
            '{{ phpVariable }}' => $phpVariable,
            '{{phpVariable}}' => $phpVariable,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Guess the model name based in Finder name or return a default model name.
     *
     * @param  string  $name
     * @return string
     */
    protected function guessModelName($name)
    {
        if (Str::endsWith($name, 'Finder')) {
            $name = substr($name, 0, -6);
        }

        $modelName = $this->qualifyModel($name);

        if (class_exists($modelName)) {
            return $modelName;
        }

        if (is_dir(app_path('Models/'))) {
            return $this->rootNamespace() . 'Models\Model';
        }

        return $this->rootNamespace() . 'Model';
    }

    /**
     * Get the namespace for the model
     *
     * @param string $name
     * @return string
     */
    protected function getNamespaceModel($name)
    {
        return $this->option('model')
            ? $this->qualifyModel($this->option('model'))
            : $this->qualifyModel($this->guessModelName($name));
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        if (!$this->option('model')) {
            $this->error('You should specify the --model option.');

            return false;
        }

        $model = $this->option('model');
        $finderDirectoryName = Str::plural($model);
        $finderPath = app_path("Finders/{$finderDirectoryName}");

        if (!File::isDirectory($finderPath)) {
            $this->error("{$finderDirectoryName} finder not found. Please create a finder first runnig 'php artisan make:finder {$finderDirectoryName}Finder --model={$model}'.");

            return false;
        }

        parent::handle();
    }
}
