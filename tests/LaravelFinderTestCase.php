<?php

namespace Backfron\LaravelFinder\Tests;

use Orchestra\Testbench\TestCase;
use Backfron\LaravelFinder\LaravelFinderServiceProvider;

abstract class LaravelFinderTestCase extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [
            LaravelFinderServiceProvider::class,
        ];
    }
}
