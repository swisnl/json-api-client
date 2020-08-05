# { json:api } Client

[![PHP from Packagist](https://img.shields.io/packagist/php-v/swisnl/json-api-client.svg)](https://packagist.org/packages/swisnl/json-api-client)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/swisnl/json-api-client.svg)](https://packagist.org/packages/swisnl/json-api-client)
[![Software License](https://img.shields.io/packagist/l/swisnl/json-api-client.svg)](LICENSE)
[![Build Status](https://travis-ci.org/swisnl/json-api-client.svg?branch=master)](https://travis-ci.org/swisnl/json-api-client)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/swisnl/json-api-client.svg)](https://scrutinizer-ci.com/g/swisnl/json-api-client/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/swisnl/json-api-client.svg)](https://scrutinizer-ci.com/g/swisnl/json-api-client/?branch=master)
[![Made by SWIS](https://img.shields.io/badge/%F0%9F%9A%80-made%20by%20SWIS-%23D9021B.svg)](https://www.swis.nl)

A PHP package for mapping remote [JSON:API](http://jsonapi.org/) resources to Eloquent like models and collections.

:bulb: Before we start, please note that this library can only be used for [JSON:API](http://jsonapi.org/) resources and requires some basic knowledge of the specification. If you are not familiar with {json:api}, please read [the excellent blog](https://laravel-news.com/json-api-introduction) by [Björn Brala](https://github.com/bbrala) for a quick introduction.


## Installation

``` bash
composer require swisnl/json-api-client
```

N.B. Make sure you have installed a PSR-18 HTTP Client before you install this package or install one at the same time e.g. `composer require swisnl/json-api-client guzzlehttp/guzzle:^7.0`.

### HTTP Client

We are decoupled from any HTTP messaging client with the help of [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18/).
This requires an extra package providing [psr/http-client-implementation](https://packagist.org/providers/psr/http-client-implementation).
To use Guzzle 7, for example, simply require `guzzlehttp/guzzle`:

``` bash
composer require guzzlehttp/guzzle:^7.0
```

See [Bind clients](#bind-clients) if you want to use your own HTTP client or use specific configuration options.

### Laravel

This package includes some Laravel specific classes to make it easier to use it with Laravel.

#### Service Provider and Facade Aliases

The required service provider and some facade aliases are automatically discovered by Laravel.
However, if you've disabled package auto discover, you must add the service provider and optionally the facade aliases to your `config/app.php` file:

``` php
'providers' => [
    ...,
    \Swis\JsonApi\Client\Providers\ServiceProvider::class,
],
'aliases' => [
    ...,
    'DocumentFactory' => \Swis\JsonApi\Client\Facades\DocumentFactoryFacade::class,
    'DocumentParser' => \Swis\JsonApi\Client\Facades\DocumentParserFacade::class,
    'ItemHydrator' => \Swis\JsonApi\Client\Facades\ItemHydratorFacade::class,
    'ResponseParser' => \Swis\JsonApi\Client\Facades\ResponseParserFacade::class,
    'TypeMapper' => \Swis\JsonApi\Client\Facades\TypeMapperFacade::class,
],
```

#### Configuration

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
        
If you would like to make changes to the default configuration, publish and edit the configuration file:

``` bash
php artisan vendor:publish --provider="Swis\JsonApi\Client\Providers\ServiceProvider" --tag="config"
```


## Getting started

You can simply require the [DocumentClient](#documentclient) as a dependency and use it in your class.
Alternatively, you can create a repository based on `\Swis\JsonApi\Client\Repository` and use that as a dependency:

``` php
use Swis\JsonApi\Client\Repository

class BlogRepository extends Repository
{
    protected $endpoint = 'blogs';
}

$repository = app(BlogRepository::class);
$document = $repository->all();
if ($document->hasErrors()) {
   // do something with errors
} else {
  $blogs = $document->getData();
}
```


## Items

By default, all items are an instance of `\Swis\JsonApi\Client\Item`.
The `Item` extends [jenssegers/model](https://github.com/jenssegers/model), which provides a Laravel Eloquent-like base class.
Please see it's documentation about the features it provides.
You can define your own models by extending `\Swis\JsonApi\Client\Item` or by implementing the `\Swis\JsonApi\Client\Interfaces\ItemInterface` yourself.
This can be useful if you want to define, for example, hidden attributes, casts or get/set mutators.
If you use custom models, you must register them with the [TypeMapper](#typemapper).

### Relations

On top of [jenssegers/model](https://github.com/jenssegers/model), this package has implemented [Laravel Eloquent-like relations](https://laravel.com/docs/eloquent-relationships).
These relations provide a fluent interface to retrieve the related items.
There are currently four relations available:

 * `HasOneRelation`
 * `HasManyRelation`
 * `MorphToRelation`
 * `MorphToManyRelation`

Please see the following example about defining the relationships:

``` php
use Swis\JsonApi\Client\Item;

class AuthorItem extends Item
{
    protected $type = 'author';

    public function blogs()
    {
        return $this->hasMany(BlogItem::class);
    }
}

class BlogItem extends Item
{
    protected $type = 'blog';

    public function author()
    {
        return $this->hasOne(AuthorItem::class);
    }
}
```

#### Naming support

Relations should be defined using camelCase methods.
Related items can then be accessed via magic attributes in camelCase or snake_case or by using the explicit name you used when defining the relation.


## Collections

This package uses [Laravel Collections](https://laravel.com/docs/collections) as a wrapper for item arrays.


## Links

All objects that can have links (i.e. document, error, item and relationship) use `Concerns/HasLinks` and thus have a `getLinks` method that returns an instance of `Links`.
This is a simple array-like object with key-value pairs which are in turn an instance of `Link` or `null`.

### Example

Given the following JSON:
``` json
{
	"links": {
		"self": "http://example.com/articles"
	},
	"data": [{
		"type": "articles",
		"id": "1",
		"attributes": {
			"title": "JSON:API paints my bikeshed!"
		},
		"relationships": {
			"author": {
				"data": {
					"type": "people",
					"id": "9"
				},
				"links": {
					"self": "http://example.com/articles/1/author"
				}
			}
		},
		"links": {
			"self": "http://example.com/articles/1"
		}
	}]
}
```

You can get the links this way:
``` php
/** @var $document \Swis\JsonApi\Client\Document */

// Document links
$links = $document->getLinks();
echo $links->self->getHref(); // http://example.com/articles

// Item links
$links = $document->getData()->getLinks();
echo $links->self->getHref(); // http://example.com/articles/1

// Relationship links
$links = $document->getData()->author()->getLinks();
echo $links->self->getHref(); // http://example.com/articles/1/author
```


## Meta

All objects that can have meta information (i.e. document, error, item, jsonapi, link and relationship) use `Concerns/HasMeta` and thus have a `getMeta` method that returns an instance of `Meta`.
This is a simple array-like object with key-value pairs.

### Example

Given the following JSON:
``` json
{
	"links": {
		"self": {
			"href": "http://example.com/articles/1",
			"meta": {
				"foo": "bar"
			}
		}
	},
	"data": {
		"type": "articles",
		"id": "1",
		"attributes": {
			"title": "JSON:API paints my bikeshed!"
		},
		"relationships": {
			"author": {
				"data": {
					"type": "people",
					"id": "9"
				},
				"meta": {
					"written_at": "2019-07-16T13:47:26"
				}
			}
		},
		"meta": {
			"copyright": "Copyright 2015 Example Corp."
		}
	},
	"meta": {
		"request_id": "a77ab2b4-7132-4782-8b5e-d94ebaff6e13"
	}
}
```

You can get the meta this way:
``` php
/** @var $document \Swis\JsonApi\Client\Document */

// Document meta
$meta = $document->getMeta();
echo $meta->request_id; // a77ab2b4-7132-4782-8b5e-d94ebaff6e13

// Link meta
$meta = $document->getLinks()->self->getMeta();
echo $meta->foo; // bar

// Item meta
$meta = $document->getData()->getMeta();
echo $meta->copyright; // Copyright 2015 Example Corp.

// Relationship meta
$meta = $document->getData()->author()->getMeta();
echo $meta->written_at; // 2019-07-16T13:47:26
```


## TypeMapper

All custom models must be registered with the `TypeMapper`.
This `TypeMapper` maps, as the name suggests, JSON:API types to custom [items](#items).

### Mapping types

You can manually register items with the `\Swis\JsonApi\Client\TypeMapper` or use the supplied `\Swis\JsonApi\Client\Providers\TypeMapperServiceProvider`:

``` php
use Swis\JsonApi\Client\Providers\TypeMapperServiceProvider as BaseTypeMapperServiceProvider;

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


## Repository

For convenience, this package includes a basic repository with several methods to work with [resources](https://jsonapi.org/format/#document-resource-objects).
You can create a repository for each of the endpoints you use based on `\Swis\JsonApi\Client\Repository`.
This repository then uses standard CRUD endpoints for all its actions.

``` php
class BlogRepository extends \Swis\JsonApi\Client\Repository
{
    protected $endpoint = 'blogs';
}
```

If this repository (pattern) doesn't fit your needs, you can create your own implementation using the [clients](#clients) provided by this package.

### Request parameters

All methods provided by the repository take extra parameters that will be appended to the url.
This can be used, among other things, to add [include](https://jsonapi.org/format/#fetching-includes) and/or [pagination](https://jsonapi.org/format/#fetching-pagination) parameters:

``` php
$repository = new BlogRepository();
$repository->all(['include' => 'author', 'page' => ['limit' => 15, 'offset' => 0]]);
```


## ItemHydrator

The `ItemHydrator` can be used to fill/hydrate an item and its relations using an associative array with attributes.
This is useful if you would like to hydrate an item with POST data from your request:

``` php
$typeMapper = app(Swis\JsonApi\Client\TypeMapper::class);
$itemHydrator = app(Swis\JsonApi\Client\ItemHydrator::class);
$blogRepository = app(App\Repositiories\BlogRepository::class);

$item = $itemHydrator->hydrate(
    $typeMapper->getMapping('blog'),
    request()->all(['title', 'author', 'date', 'content', 'tags']),
    request()->id
);
$blogRepository->save($item);
```

### Relations

The `ItemHydrator` also hydrates (nested) relations.
A relation must explicitly be listed on the item in the `$availableRelations` array in order to be hydrated.
If we take the above example, we can use the following attributes array to hydrate a new blog item:

``` php
$attributes = [
    'title'   => 'Introduction to JSON:API',
    'author'  => [
        'id'       => 'f1a775ef-9407-40ba-93ff-7bd737888dc6',
        'name'     => 'Björn Brala',
        'homepage' => 'https://github.com/bbrala',
    ],
    'co-author' => null,
    'date'    => '2018-12-02 15:26:32',
    'content' => 'JSON:API was originally drafted in May 2013 by Yehuda Katz...',
    'media' => [],
    'tags'    => [
        1,
        15,
        56,
    ],
];
$itemDocument = $itemHydrator->hydrate($typeMapper->getMapping('blog'), $attributes);

echo json_encode($itemDocument, JSON_PRETTY_PRINT);

{
    "data": {
        "type": "blog",
        "attributes": {
            "title": "Introduction to JSON:API",
            "date": "2018-12-02 15:26:32",
            "content": "JSON:API was originally drafted in May 2013 by Yehuda Katz..."
        },
        "relationships": {
            "author": {
                "data": {
                    "type": "author",
                    "id": "f1a775ef-9407-40ba-93ff-7bd737888dc6"
                }
            },
            "co-author": {
                "data": null
            },
            "media": {
                "data": []
            },
            "tags": {
                "data": [{
                    "type": "tag",
                    "id": "1"
                }, {
                    "type": "tag",
                    "id": "15"
                }, {
                    "type": "tag",
                    "id": "56"
                }]
            }
        }
    },
    "included": [{
        "type": "author",
        "id": "f1a775ef-9407-40ba-93ff-7bd737888dc6",
        "attributes": {
            "name": "Björn Brala",
            "homepage": "https://github.com/bbrala"
        }
    }]
}
```

As you can see in this example, relations can be hydrated by id, or by an associative array with an id and more attributes.
If the item is hydrated using an associative array, it will be included in the resulting json unless `setOmitIncluded(true)` is called on the relation.
You can unset a relation by passing `null` for singular relations or an empty array for plural relations.

N.B. Morph relations require a 'type' attribute to be present in the data in order to know which type of item should be created.


## Handling errors

A request can fail due to several reasons and how this is handled depends on what happened.
If the `DocumentClient` encounters an error there are basically three options.

#### Non 2xx request without body

If a response does not have a successful status code (2xx) and does not have a body, the `DocumentClient` (and therefore also the `Repository`)  will return an instance of `InvalidResponseDocument`.

#### Non 2xx request with invalid JSON:API body

If a response does not have a successful status code (2xx) and does have a body, it is parsed as if it's a JSON:API document.
If the response can not be parsed as such document, a `ValidationException` will be thrown.

#### Non 2xx request with valid JSON:API body

If a response does not have a successful status code (2xx) and does have a body, it is parsed as if it's a JSON:API document.
In this case the `DocumentClient` (and therefore also the `Repository`)  will return an instance of `Document`.
This document contains the errors from the response, assuming the server responded with errors.

### Checking for errors

Based on the above rules you can check for errors like this:

``` php
$document = $repository->all();

if ($document instanceof InvalidResponseDocument || $document->hasErrors()) {
    // do something with errors
}
```


## Clients

This package offers two clients; `DocumentClient` and `Client`.
   
### DocumentClient

This is the client that you would generally use e.g. the repository uses this client internally.
Per the [JSON:API spec](http://jsonapi.org/format/#document-structure), all requests and responses are documents.
Therefore, this client always expects a `\Swis\JsonApi\Client\Interfaces\DocumentInterface` as input when posting data and always returns this same interface.
This can be a plain `Document` when there is no data, an `ItemDocument` for an item, a `CollectionDocument` for a collection or an `InvalidResponseDocument` when the server responds with a non 2xx response.

The `DocumentClient` follows the following steps internally:
 1. Send the request using your HTTP client;
 2. Use `ResponseParser` to parse and validate the response;
 3. Create the correct document instance;
 4. Hydrate every item by using the item model registered with the `TypeMapper` or a `\Swis\JsonApi\Client\Item` as fallback;
 5. Hydrate all relationships;
 6. Add meta data to the document such as [errors](http://jsonapi.org/format/#errors), [links](http://jsonapi.org/format/#document-links) and [meta](http://jsonapi.org/format/#document-meta).

### Client

This client is a more low level client and can be used, for example, for posting binary data such as images.
It can take everything your request factory takes as input data and returns the 'raw' `\Psr\Http\Message\ResponseInterface`.
It does not parse or validate the response or hydrate items!


## DocumentFactory

The `DocumentClient` requires `ItemDocumentInterface` instances when creating or updating resources.
Such documents can easily be created using the `DocumentFactory` by giving it a `DataInterface` instance.
This can be an `ItemInterface`, usually created by the [ItemHydrator](#itemhydrator), or a `Collection`.


## Service Provider

The `\Swis\JsonApi\Client\Providers\ServiceProvider` registers the `TypeMapper`, `JsonApi\Parser` and both clients; `Client` and `DocumentClient`.
Each section can be overwritten to allow extended customization.

### Bind TypeMapper

The service provider registers the `TypeMapper` as a singleton so your entire application has the same mappings available.

### Bind Clients

The service provider registers the `Client` and `DocumentClient` to your application.
By default the `Client` uses [php-http/discovery](https://github.com/php-http/discovery) to find an available HTTP client, request factory and stream factory so you don't have to setup those yourself.
You can specify your own HTTP client, request factory or stream factory by customizing the container binding.
This is a perfect way to add extra options to your HTTP client or register a mock HTTP client for your tests:

``` php
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected function register()
    {
        $this->app->bind(\Swis\JsonApi\Client\Client::class, function ($app) {
            if ($app->environment('testing')) {
                $httpClient = new \Swis\Http\Fixture\Client(
                    new \Swis\Http\Fixture\ResponseBuilder('/path/to/fixtures');
                );
            } else {
                $httpClient = \Http\Adapter\Guzzle6\Client::createWithConfig(
                    [
                        'timeout' => 2,
                    ]
                );
            }
    
            return new \Swis\JsonApi\Client\Client($httpClient);
        });
    }
}
```

N.B. This example uses our [swisnl/php-http-fixture-client](https://github.com/swisnl/php-http-fixture-client) when in testing environment.
This package allows you to easily mock requests with static fixtures.
Definitely worth a try!


## Advanced usage

If you don't like to use the supplied repository or clients, you can also parse a 'raw' `\Psr\Http\Message\ResponseInterface` or a simple json string using the `Parsers\ResponseParser` or `Parser\DocumentParser` respectively.


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Testing

``` bash
composer test
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.


## Security

If you discover any security related issues, please email security@swis.nl instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


## SWIS :heart: Open Source

[SWIS](https://www.swis.nl) is a web agency from Leiden, the Netherlands. We love working with open source software.
