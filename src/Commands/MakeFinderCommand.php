<?php

namespace Backfron\LaravelFinder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class MakeFinderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:finder {name} {--model=} {--filter=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new finder.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Finder';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/finder.stub');
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
            $this->getDefaultNamespace(trim($rootNamespace, '\\')) . "\\{$subfolder}\\" . $name
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

        $replace = [
            'NamespacedDummyModel' => $namespaceModel,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Guess the subdirectory based in the Finder name.
     *
     * @param string $name
     * @return string
     */
    protected function guessSubfolderName($name)
    {
        if (Str::endsWith($name, 'Finder')) {
            return Str::plural(class_basename(substr($name, 0, -6)));
        }

        return Str::plural(class_basename($name));
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
        if ($this->option('model')) {
            $modelName = $this->guessModelName($this->option('model'));
            if (in_array($modelName, ['App\Model', 'App\Models\Model'])){
                $this->error("The specified model '" . $this->option('model') . "' was not found.");

                return false;
            }
        }

        parent::handle();

        if ($this->option('filter')) {

            $model = class_basename($this->guessModelName($this->argument('name')));
            foreach ($this->option('filter') as $filter) {
                $this->call('make:filter', [
                    'name' => $filter,
                    '--model' => $model,
                ]);
            }
        }
    }
}
