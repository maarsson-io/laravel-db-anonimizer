<?php

namespace Maarsson\LaravelDbAnonimizer\Tests;

use Illuminate\Foundation\Application;
use Maarsson\LaravelDbAnonimizer\LaravelDbAnonimizerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Register the package in Testbench's isolated Laravel application.
     *
     * @param Application $app
     *
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [LaravelDbAnonimizerServiceProvider::class];
    }
}
