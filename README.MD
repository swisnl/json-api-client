# { json:api } Client

[![PHP from Packagist](https://img.shields.io/packagist/php-v/swisnl/jsonapi.svg)](https://packagist.org/packages/swisnl/jsonapi)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/swisnl/jsonapi.svg)](https://packagist.org/packages/swisnl/jsonapi)
[![Software License](https://img.shields.io/packagist/l/swisnl/jsonapi.svg)](LICENSE)
[![Run Status](https://api.shippable.com/projects/59ae7fe68a02980600016631/badge?branch=master)](https://app.shippable.com/github/swisnl/jsonapi)
[![Coverage Badge](https://api.shippable.com/projects/59ae7fe68a02980600016631/coverageBadge?branch=master)](https://app.shippable.com/github/swisnl/jsonapi) 

A PHP package for mapping remote [{json:api}](http://jsonapi.org/) resources to Eloquent like models and collections.


## Installation

``` bash
composer require swisnl/jsonapi
```

### HTTP Client

We are decoupled from any HTTP messaging client with the help of [PHP-HTTP](http://php-http.org/).
This requires another package providing [php-http/client-implementation](https://packagist.org/providers/php-http/client-implementation).
To use Guzzle 6, for example, simply require `php-http/guzzle6-adapter`:

``` bash
composer require php-http/guzzle6-adapter
```

### Laravel Service Provider

If you are using Laravel < 5.5 or have disabled package auto discover, you must add the service provider to your `config/app.php` file:

``` php
'providers' => [
    ...,
    Swis\JsonApi\Client\Providers\ServiceProvider::class,
],
```


## Getting started

You can simply require the client as a dependency and use it in your class.
You can, for example, make a repository that uses the DocumentClient:

``` php
use Swis\JsonApi\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Interfaces\ItemDocumentInterface;

class BlogRepository
{
    protected $client;

    public function __construct(DocumentClientInterface $client)
    {
        $this->client = $client;
    }

    public function all(array $parameters = [])
    {
        return $this->client->get('blogs?'.http_build_query($parameters));
    }

    public function create(ItemDocumentInterface $document, array $parameters = [])
    {
        return $this->client->post('blogs?'.http_build_query($parameters), $document);
    }

    public function find(string $id, array $parameters = [])
    {
        return $this->client->get('blogs/'.urlencode($id).'?'.http_build_query($parameters));
    }

    public function update(ItemDocumentInterface $document, array $parameters = [])
    {
        return $this->client->patch('blogs/'.urlencode($document->getData()->getId()).'?'.http_build_query($parameters), $document);
    }

    public function delete(string $id, array $parameters = [])
    {
        return $this->client->delete('blogs/'.urlencode($id).'?'.http_build_query($parameters));
    }
}
```

## Configuration

The following is the default configuration provided by this package:

``` php
return [
    /*
    |--------------------------------------------------------------------------
    | Base URI
    |--------------------------------------------------------------------------
    |
    | Specify a base uri which will be prepended to every URI.
    |
    | Default: empty string
    |
    */
    'base_uri' => '',
];
```
        
### Publish Configuration

If you would like to make changes to the default configuration, publish and edit the configuration file:

``` bash
php artisan vendor:publish --provider="Swis\JsonApi\Client\Providers\ServiceProvider" --tag="config"
```


## Package flow
   
Short description
   
### DocumentClient

### Client (domme versie voor bijvoorbeeld binary bestanden)


## TypeMapper

By default, all objects are an instance of `JenssegersItem`.
You can, however, define your own items by registering them with the `TypeMapper`.
This `TypeMapper` maps JSON API types to custom item models (please see below for more information about item models).
This can be useful if you want to define, for example, hidden attributes, casts or get/set mutators.

### Mapping types

You can manually register item with the `\Swis\JsonApi\Client\TypeMapper` or use the supplied `\Swis\JsonApi\Client\Providers\TypeMapperServiceProvider`:

``` php
use Swis\JsonApi\Providers\TypeMapperServiceProvider as BaseTypeMapperServiceProvider;

class TypeMapperServiceProvider extends BaseTypeMapperServiceProvider
{
    /**
     * A list of class names implementing \Swis\JsonApi\Client\Interfaces\ItemInterface.
     *
     * @var string[]
     */
    protected $items = [
        \App\Items\Author::class,
        \App\Items\Blog::class,
        \App\Items\Comment::class,
    ];
}
```


## Item models

Short description

### Jenssegers

Explain relations.

### Eloquent (EXPERIMENTAL)

## Collections


## ServiceProvider

Short description

### Bind TypeMapper

### Bind Parser

### Bind Clients


## Todo

 - [ ] CHANGELOG.MD
 - [ ] Increase test coverage


## Testing

``` bash
composer test
```


## Security

If you discover any security related issues, please email service@swis.nl instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
