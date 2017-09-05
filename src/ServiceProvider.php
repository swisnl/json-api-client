<?php

namespace Swis\JsonApi;

use Art4\JsonApiClient\Utils\Manager as JsonApiClientManger;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Swis\JsonApi\Client as ApiClient;
use Swis\JsonApi\DocumentClient as ApiDocumentClient;
use Swis\JsonApi\Guzzle\FixturesHandler;
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
                    $this->getGuzzleClient(),
                    config('jsonapi.base_uri'),
                    new RequestFactory(),
                    new ResponseFactory()
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

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getGuzzleClient(): GuzzleClient
    {
        return new GuzzleClient(
            [
                'base_uri' => config('jsonapi.base_uri'),
                'headers'  => [
                    'Accept'       => 'application/vnd.api+json',
                    'Content-Type' => 'application/vnd.api+json',
                ],
                'handler'  => $this->registerGuzzleHandlers(),
            ]
        );
    }

    /**
     * @return \GuzzleHttp\HandlerStack
     */
    protected function registerGuzzleHandlers(): HandlerStack
    {
        if (app()->environment('testing')) {
            $handler = new FixturesHandler(config('jsonapi.fixtures.path'));
            $handler->setDomainAliases(config('jsonapi.fixtures.domain_aliases', []));
            $stack = HandlerStack::create($handler);
        } else {
            $stack = HandlerStack::create();
        }

        return $stack;
    }
}
