<?php

namespace Swis\JsonApi\Client\Tests;

use Faker\Factory;
use Orchestra\Testbench\TestCase;

abstract class AbstractTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    protected function setUp()
    {
        $this->faker = Factory::create();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Import default settings
        $defaultSettings = require __DIR__.'/../config/jsonapi.php';
        $app['config']->set('jsonapi', $defaultSettings);
    }

    protected function getPackageProviders($app)
    {
        return [];
    }
}
