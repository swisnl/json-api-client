<?php

namespace Swis\JsonApi\Client\Providers;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Swis\JsonApi\Client\Client;
use Swis\JsonApi\Client\DocumentClient;
use Swis\JsonApi\Client\Interfaces\ClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentParserInterface;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\Parsers\DocumentParser;
use Swis\JsonApi\Client\Parsers\ResponseParser;
use Swis\JsonApi\Client\TypeMapper;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__, 2).'/config/' => config_path(),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2).'/config/jsonapi.php',
            'jsonapi'
        );

        $this->registerSharedTypeMapper();
        $this->registerParsers();
        $this->registerClients();
    }

    protected function registerSharedTypeMapper()
    {
        $this->app->singleton(TypeMapperInterface::class, TypeMapper::class);
    }

    protected function registerParsers()
    {
        $this->app->bind(DocumentParserInterface::class, DocumentParser::class);
        $this->app->bind(ResponseParserInterface::class, ResponseParser::class);
    }

    protected function registerClients()
    {
        $this->app->bind(
            Client::class,
            function () {
                return new Client(
                    $this->getHttpClient(),
                    config('jsonapi.base_uri'),
                    $this->getMessageFactory()
                );
            }
        );

        $this->app->bind(ClientInterface::class, Client::class);
        $this->app->bind(DocumentClientInterface::class, DocumentClient::class);
    }

    protected function getHttpClient(): HttpClient
    {
        return HttpClientDiscovery::find();
    }

    protected function getMessageFactory(): MessageFactory
    {
        return MessageFactoryDiscovery::find();
    }
}
