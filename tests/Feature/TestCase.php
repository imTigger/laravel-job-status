<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Imtigger\LaravelJobStatus\LaravelJobStatusServiceProvider;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__ . '/../../database/migrations'));
        $this->loadMigrationsFrom(realpath(__DIR__ . '/../database/migrations'));
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
