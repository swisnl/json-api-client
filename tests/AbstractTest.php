<?php

abstract class AbstractTest extends Orchestra\Testbench\TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    protected function setUp()
    {
        $this->faker = \Faker\Factory::create();
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
