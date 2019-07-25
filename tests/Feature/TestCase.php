<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Imtigger\LaravelJobStatus\LaravelJobStatusServiceProvider;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__.'/../../database/migrations'));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelJobStatusServiceProvider::class,
            ConsoleServiceProvider::class,
        ];
    }

}
