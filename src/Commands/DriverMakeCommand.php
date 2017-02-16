<?php

namespace TaylorNetwork\API\Commands;

use Illuminate\Console\GeneratorCommand;

class DriverMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'api:driver {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API driver class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'API Driver Class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/driver.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . config('api.namespace', 'APIDrivers');
    }
}