# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

* Add `isSuccess` method to Document [#40](https://github.com/swisnl/json-api-client/pull/40)

### Changed

* Add headers to request methods in `DocumentClientInterface`.
N.B. This is a breaking change if you implement the interface yourself or extend the `DocumentClient`. [#34](https://github.com/swisnl/json-api-client/pull/34)
* `Repository` doesn't throw exceptions anymore. [#41](https://github.com/swisnl/json-api-client/pull/41)
N.B. This is a breaking change if you catch `DocumentNotFoundException` or `DocumentTypeException`. If you would like the old behaviour, you can simply extend the `Repository` and implement it yourself.

### Removed

* Removed obsolete `ItemDocumentSerializer` in favor of `JsonSerializable`.
N.B. This is a breaking change if you use this class directly, construct the `DocumentClient` yourself or have overwritten `\Swis\JsonApi\Client\Providers\ServiceProvider::registerClients`. The `ItemDocument` can now be serialized using its `jsonSerialize` method.
* Removed obsolete `DocumentNotFoundException` and `DocumentTypeException`. [#41](https://github.com/swisnl/json-api-client/pull/41)
N.B. This is a breaking change if you catch these exceptions.

### Fixed

* Do not fail on, but skip relationships without data [#38](https://github.com/swisnl/json-api-client/pull/38)

## [0.11.0] - 2018-12-21

### Changed

* Implement `JsonSerializable` in `Document` [#32](https://github.com/swisnl/json-api-client/pull/32)
* Add toArray to `DocumentInterface` and `Document` so `CollectionDocument` and `ItemDocument` now share the same toArray method [#32](https://github.com/swisnl/json-api-client/pull/32) 

## [0.10.3] - 2018-10-26

### Fixed

* Omit 'type' attribute when filling attributes for a morph relationship [#28](https://github.com/swisnl/json-api-client/pull/28)

## [0.10.2] - 2018-10-24

### Fixed

* Allow '0' as id in ItemDocumentBuilder [#27](https://github.com/swisnl/json-api-client/pull/27)

## [0.10.1] - 2018-09-25

### Fixed

* Add the id when the item has one and not only when it is not new

## [0.10.0] - 2018-09-10

### Added

* Added Laravel 5.7 support

## [0.9.0] - 2018-08-30

### Added

* Added Laravel 5.6 support
* Travis now runs tests on all supported versions of Laravel 

## [0.8.0] - 2018-08-14

### Changed

* Refactored name(space) of `\Swis\JsonApi\Client\Items\JenssegersItem` to `\Swis\JsonApi\Client\Item` as we only have one item now.

### Removed

* `EloquentItem` is removed because it had some limitations which could not be fixed without being too opinionated.
* `NullItem` is removed in favor of simply `null`. This item was only used internally so this should not affect you.

## [0.7.5] - 2018-07-04

### Fixed

* Do not add attributes to item when empty [#20](https://github.com/swisnl/json-api-client/pull/20)

## [0.7.4] - 2018-06-20

### Fixed

* Reverted the behaviour of handling duplicate items back to what it was in <= 0.7.2. This change in behaviour was introduced in the last performance update (0.7.3). N.B. This change will only affect you if you parse documents with duplicate items, which violates the JSON API spec.

## [0.7.3] - 2018-06-01

### Changed

* Improved performance of JsonApi\Hydrator [#18](https://github.com/swisnl/json-api-client/pull/18)

## [0.7.2] - 2018-05-17

### Fixed

* Fixed building the request with headers

## [0.7.1] - 2018-05-16

### Fixed

* Corrected the path to the config file

## [0.7.0] - 2018-05-11

### Changed

#### Update art4/json-api-client to latest version, this changes some of the returned classes.

The following classes are changed in some arguments and some method returns, if you extended or directly use `\Swis\JsonApi\Client\JsonApi\Hydrator` or `\Swis\JsonApi\Client\JsonApi\Parser` please check your code.

```
\Art4\JsonApiClient\Resource\CollectionInterface -> \Art4\JsonApiClient\ResourceCollectionInterface
\Art4\JsonApiClient\Resource\ItemInterface -> \Art4\JsonApiClient\ResourceItemInterface
\Art4\JsonApiClient\Resource\IdentifierCollection -> \Art4\JsonApiClient\ResourceIdentifierCollection
\Art4\JsonApiClient\Resource\Identifier -> \Art4\JsonApiClient\ResourceIdentifier
\Art4\JsonApiClient\Resource\Collection -> \Art4\JsonApiClient\ResourceCollection
```

## [0.6.0] - 2018-03-06

### Added

* Added CHANGELOG.md

### Changed

* Changed package name in composer.json 
* Changed autodiscovery service provider in composer.json 

## [0.5.0] - 2018-03-06

### Added 

* Added a proper README with instructions.

### Changed

* Updated namespace to `Swis\JsonApi\Client` for consistency.
* Split service provider binds so overwriting is easier.

### Removed

* Extracted fixtures client to  [swisnl/php-http-fixture-client](https://github.com/swisnl/php-http-fixture-client) and [swisnl/guzzle-fixture-handler](https://github.com/swisnl/guzzle-fixture-handler)
* Removed resources as it was redundant.

## [0.4.0] - 2018-01-16

### Changed

Refactored to use [php-http/httpplug](http://docs.php-http.org/en/latest/index.html) instead of Guzzle so the library doesn't depend on a specific http client implementation.

This does change how you customize the ServiceProvider, just include an adapter for the client you want to use and the library uses [autodiscovery](http://docs.php-http.org/en/latest/discovery.html) to find the correct [adapter/client](http://docs.php-http.org/en/latest/clients.html). It also uses a [MessageFactory](http://docs.php-http.org/en/latest/message/message-factory.html) for creating Requests and Response objects.

So for example, if you want to use Guzzle, just `composer require php-http/guzzle6-adapter`, and all should be swell.

In order to have a working mock client for testing you now need to include [php-http/mock-client](http://docs.php-http.org/en/latest/clients/mock-client.html). Which is an easy way to mock requests.
