<?php

namespace Swis\JsonApi;

use Art4\JsonApiClient\Utils\Manager as JsonApiClientManger;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Swis\JsonApi\Client as ApiClient;
use Swis\JsonApi\DocumentClient as ApiDocumentClient;
use Swis\JsonApi\Fixtures\FixtureResponseBuilder;
use Swis\JsonApi\Fixtures\FixturesClient;
use Swis\JsonApi\Interfaces\ClientInterface as ApiClientInterface;
use Swis\JsonApi\Interfaces\DocumentClientInterface as ApiDocumentClientInterface;
use Swis\JsonApi\Interfaces\ParserInterface;
use Swis\JsonApi\Interfaces\TypeMapperInterface;
use Swis\JsonApi\JsonApi\ErrorsParser;
use Swis\JsonApi\JsonApi\Hydrator;
use Swis\JsonApi\JsonApi\Parser;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__).'/config/' => config_path(),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/jsonapi.php',
            'jsonapi'
        );

        $this->registerSharedTypeMapper();
        $this->registerApiClients();
    }

    protected function registerSharedTypeMapper()
    {
        $this->app->singleton(
            TypeMapperInterface::class,
            function () {
                return new TypeMapper();
            }
        );
    }

    protected function registerApiClients()
    {
        $this->app->bind(
            ApiClientInterface::class,
            function () {
                return new ApiClient(
                    $this->getHttpClient(),
                    config('jsonapi.base_uri'),
                    MessageFactoryDiscovery::find()
                );
            }
        );

        $this->app->bind(
            ParserInterface::class,
            function (Application $app) {
                return new Parser(
                    new JsonApiClientManger(),
                    new Hydrator($app->make(TypeMapperInterface::class)),
                    new ErrorsParser()
                );
            }
        );

        $this->app->bind(
            ApiDocumentClientInterface::class,
            function (Application $app) {
                return new ApiDocumentClient(
                    $app->make(ApiClientInterface::class),
                    new ItemDocumentSerializer(),
                    $app->make(ParserInterface::class)
                );
            }
        );
    }

    protected function getHttpClient(): HttpClient
    {
        if (app()->environment('testing')) {
            return $this->getFixturesClient();
        }

        return HttpClientDiscovery::find();
    }

    /**
     * @return FixturesClient
     */
    protected function getFixturesClient(): FixturesClient
    {
        $httpClient = new FixturesClient(
            new FixtureResponseBuilder(
                config('jsonapi.fixtures.path'),
                config('jsonapi.fixtures.domain_aliases', [])
            )
        );

        return $httpClient;
    }
}
